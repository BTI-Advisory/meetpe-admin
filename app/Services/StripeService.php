<?php

namespace App\Services;
use Stripe\Account;
use Stripe\Token;
use Stripe\AccountLink;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\AccountExternalAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\CardException;
use App\Models\FailedPayout;
use App\Models\Payout;
use App\Models\Guide;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Stripe\StripeClient;

class StripeService
{


    /**
     * 
     * partie stripe CONNECT GUIDE 
     * 
     * 
     * */ 
    public function checkAccountStatus($accountId)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $account = Account::retrieve($accountId);
        return $account->requirements; // Liste des champs manquants
    }

    public function generateAccountLink($accountId)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try
        {
            $link = AccountLink::create([
                'account'     => $accountId,
                'refresh_url' => route('account.refresh', $accountId),
                'return_url'  => route('account.return', $accountId),
                'type'        => 'account_onboarding',
            ]);

            return $link->url;
        }catch(ApiErrorException $stripeException)
        {
            Log::channel('stripe_connect')->error($stripeException->getMessage());
            return false;
        }
    }

    private function isConnectedAccountAlreadyCreated($accountId)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        try
        {
            $account = $stripe->accounts->retrieve($accountId, []);
            Log::channel('stripe_connect')->info('CHECK_CONNECT_ACCOUNT_'.$accountId.' => EXISTS');
            return $account;
        }catch(ApiErrorException $stripeException)
        {
            Log::channel('stripe_connect')->info('CHECK_CONNECT_ACCOUNT_'.$accountId.' => NOT EXISTS');
            Log::channel('stripe_connect')->error($stripeException->getMessage());
            return false;
        }
        
    }

    private function createStripeConnectedAccount($user)
    {

        try
        {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));


            $accountData = [
                'type' => 'express',
                'business_type' => ($user->siren_number && $user->siren_number != "null" && !is_null($user->siren_number))
                    ? 'company'
                    : 'individual',
                'capabilities' => [
                    'transfers'     => ['requested' => true],
                    'card_payments' => ['requested' => true],
                ],
                'default_currency' => 'eur',
            ];

            if ($user->siren_number && $user->siren_number != "null" && !is_null($user->siren_number)) {
                $accountData['company'] = [
                    'name'                => $user->name_of_company,
                    'registration_number' => $user->siren_number,
                ];
            } else {
                $accountData['individual'] = [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ];
            }

            Log::channel('stripe_connect')->info('ACCOUNT DATA SENT: ' . json_encode($accountData));
            // Création directe du compte Stripe Connect
            $account = Account::create($accountData);
            Log::channel('stripe_connect')->info('CREATE_CONNECT_ACCOUNT_SUCCESS_FOR_USER_'.$user->id);
            Log::channel('stripe_connect')->info('ACCOUNT CREATED: ' . json_encode($account));
            return $account;
        }catch(ApiErrorException $stripeException)
        {
            Log::channel('stripe_connect')->info('CREATE_CONNECT_ACCOUNT_ERROR_FOR_USER_'.$user->id. " | ".$stripeException->getMessage());
            return false;
        }
        
           
    }
    public function createStripeAccountForGuide($user)
    {

        $guide = $user->guide()->first();
        $guideStripeId = $guide->stripe_account_id;
        //check si ça existe et que c'est bien crée dans stripe
        if($guideStripeId)
        {
            $isAlreadyCreatedAccount = $this->isConnectedAccountAlreadyCreated($guideStripeId);
            //check si ça existe  reelement sur stripe
            if($isAlreadyCreatedAccount)
                {
                   //is already created on stripe
                   return $isAlreadyCreatedAccount;
                }
        }
        $account = $this->createStripeConnectedAccount($user);
        if($account)
        {
             $guide->stripe_account_id = $account->id;

             if($url = $this->generateAccountLink($account->id))
             {
                $guide->stripe_connect_form_url = $url;
             }
             $guide->save();
             return $url;
        }
        return false;
    }




    /**
     * 
     * 
     *  partie PAIEMENT voyageur
     * */


     public function createPaymentIntent($amount,$experienceTitle,$experienceId,$user)
    {

        Log::channel('stripe_payment')->info('CREATE_PAYMENT_INTENT_FOR_'.$user->id.'_FOR_EXPERIENCE_'.$experienceId);
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $stripeClient = new StripeClient(['api_key'=>config('services.stripe.secret'),'stripe_version' => '2025-06-30.preview']);
            

            // Créer le calcul de taxes
             $calculation = $stripeClient->tax->calculations->create([
                'currency' => 'eur', // adapter la devise
                'line_items' => [
                    [
                        'amount' => $amount * 100, // montant en centimes
                        'reference' => 'experience_'.$experienceId,
                        'tax_code' => 'txcd_20030000', // mettre le code fiscal adapté
                    ],
                ],
                'customer_details' => [
                    'address' => [
                        'country' => 'FR',
                    ],
                    'address_source' => 'billing',
                ],
            ]);


            // Créer un PaymentIntent
            $paymentIntent = $stripeClient->paymentIntents->create([
                'amount' => $amount*100,
                'currency' => 'eur',
                'description' => $experienceTitle,
                'payment_method_types' => ['card'],
                'hooks' => ['inputs' => ['tax' => ['calculation' => $calculation->id]]],
                'metadata' => [
                    'experience' => $experienceTitle,
                    'user' => $user->name,
                    'tax_calculation_id' => $calculation->id,
                ],
                'receipt_email'=>$user->email
            ]);
            Log::channel('stripe_payment')->info('SUCCESS_IN_CREATE_PAYMENT_INTENT_FOR_'.$user->id.'_FOR_EXPERIENCE_'.$experienceId);
            return [
                'clientSecret' => $paymentIntent->client_secret,
                'payment_intent_id'=>$paymentIntent->id
            ];
        } catch (\Exception $e) {
            Log::channel('stripe_payment')->error('ERROR_IN_CREATE_PAYMENT_INTENT_FOR_'.$user->id.'_FOR_EXPERIENCE_'.$experienceId.' : '.$e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function refundVoyageur($reservation, $refundAmount,$cause="Autre")
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $refund = \Stripe\Refund::create([
                'charge' => $reservation->stripe_charge_id, // ID de la charge Stripe
                'amount' => $refundAmount * 100, 
                'metadata' => [
                    'cause' => $cause
                ]
            ]);
            $reservation->stripe_refund_id = $refund->id;  
            $reservation->save();

            return true;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Gérer les erreurs de l'API
            Log::channel('stripe_payment')->error('ERROR_IN_CREATE_REFUND_FOR_RESERVATION'.$reservation->id.'_FOR_CHARGE_ID_'.$reservation->stripe_charge_id.' : '.$e->getMessage());
            return false;

        }
    }

    public function processPayouts(InvoiceService $invoiceService, FileService $fileService)
    {
               
            // Clé API Strip
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));



            //traiter les anciens failed payout
            $failedPayouts = FailedPayout::all();
            foreach ($failedPayouts as $failed) 
            {

                $guide = Guide::find($failed->guide_id);
                if (!$guide || !$guide->stripe_account_id) {
                    continue;
                }

                try {
                    $transfer = \Stripe\Transfer::create([
                        'amount' => $failed->payout_amount * 100,
                        'currency' => 'eur',
                        'destination' => $guide->stripe_account_id,
                        'description' => 'Régularisation du payout du mois '.$failed->month,
                    ]);

                    // ✅ Créer le payout en DB
                    $payout = Payout::create([
                        'guide_id' => $guide->guide_id,
                        'amount' => $failed->payout_amount,
                        'stripe_transfer_id' => $transfer->id,
                        'paid_at' => now(),
                        'payment_period' => $failed->month, // ✅ garder le bon mois
                    ]);

                    $month = Carbon::createFromFormat('m-Y', $failed->month);
                    $startOfMonth = $month->copy()->startOfMonth()->startOfDay();
                    $endOfMonth   = $month->copy()->endOfMonth()->endOfDay();
                    $this->generateInvoice($guide, $invoiceService, $fileService, $payout,$startOfMonth, $endOfMonth);




                    // ✅ Marquer comme success
                    $failed->delete();

                    Log::channel('stripe_payout')->info('SUCCESS_LATE_PAYOUT | '.$guide->guide_id.' | '.$failed->month);

                } catch (\Exception $e) {
                    Log::channel('stripe_payout')->error('ERROR_LATE_PAYOUT | '.$guide->guide_id.' | '.$e->getMessage());
                }
            }




            $debutMoisDernier = Carbon::now()->subMonth()->startOfMonth();
            $finMoisDernier = Carbon::now()->subMonth()->endOfMonth();


            $montantsParGuide = Reservation::join('guide_experiences', 'reservations.experience_id', '=', 'guide_experiences.id')
            ->selectRaw('guide_experiences.user_id as guide_id, SUM(reservations.guide_payout_amount) as total_payout')
            ->whereBetween('reservations.date_time', [$debutMoisDernier, $finMoisDernier])
            ->where('reservations.guide_payout_amount', '!=', 0)
            ->groupBy('guide_experiences.user_id')
            ->get();

            foreach ($montantsParGuide as $list) {
                $userId = $list->guide_id;
                $payoutAmount = $list->total_payout;

                $guide = Guide::where('user_id',$userId)->first();
                if(is_null($guide) || is_null($guide->stripe_account_id))
                {
                    $failedPayout = FailedPayout::firstOrNew(
                        ['guide_id' => $guide->guide_id] 
                    );
                    $failedPayout->payout_id = null;
                    $failedPayout->failure_message = "STRIPE ACCOUNT ID NULL";
                    $failedPayout->payout_amount = $payoutAmount;
                    $failedPayout->status = 'failed';
                    $failedPayout->month = Carbon::parse($debutMoisDernier)->format('m-Y');
                    $failedPayout->save();
                }
                else
                {

                    //ajouter un controle si le guide est déjà payé pour ce mois en question
                     $payoutGuide = Payout::where('guide_id',$guide->guide_id)
                    ->where('payment_period', $debutMoisDernier->format('m-Y'))
                    ->first();

                    if($payoutGuide)
                    {
                        
                        Log::channel('stripe_payout')->error('DOUBLE_PAYOUT | '.$guide->guide_id.' | montant déjà payé : '.$payoutGuide->amount.' | montant à payé : '.$payoutAmount .' | MOIS : '.Carbon::parse($debutMoisDernier)->format('m-Y'));
                        
                    }  
                    else
                    {
                        try
                        { 
                            if($payoutAmount>0)
                            {
                                // il faut ajouter l'appel à la generation de PDF facture et l'envoyer par mail au guide concerné
                                //ici le prix payoutamount est en ttc, il faut verifier le type de guide
                                // si PRO il faut detaillé la facture avec le HT, TVA et TTC
                                // si non il faut juste envoyé le prix TTC payoutamount
                                $transfert =  \Stripe\Transfer::create([
                                    'amount' => $payoutAmount * 100, // Montant en centimes
                                    'currency' => 'eur',
                                    'destination' => $guide->stripe_account_id,
                                    'description' => 'Payout des montants des réservations pour le guide.',
                                ]);
                                FailedPayout::where('guide_id', $guide->guide_id)
                                ->where('month',Carbon::parse($debutMoisDernier)->format('m-Y'))
                                ->delete();
        
                                // Sauvegarder le paiement dans la table `payouts`
                                $payout = Payout::create([
                                    'guide_id' => $guide->guide_id,
                                    'amount' => $payoutAmount,
                                    'stripe_transfer_id' => $transfert->id,
                                    'paid_at' => now(),
                                    'payment_period' => $debutMoisDernier->format('m-Y'),
                                ]);

    
                                $this->generateInvoice($guide, $invoiceService, $fileService, $payout,$debutMoisDernier,$finMoisDernier);
                                // ajouter ici un sauvegarde de ce transfert dans la base de données pour garder trace
                                Log::channel('stripe_payout')->info('SUCCESS_PAYOUT | '.$guide->stripe_account_id.' | '.$transfert);
                            }
                            else
                                Log::channel('stripe_payout')->error('NULL_PAYOUT | '.$guide->guide_id.' | MONTANT : '.$payoutAmount.' | MOIS : '.Carbon::parse($debutMoisDernier)->format('m-Y'));

                            
                        } 
                        catch(ApiErrorException $stripeException) {
                            $failedPayout = FailedPayout::firstOrNew(
                                ['guide_id' => $guide->guide_id] 
                            );
                            $failedPayout->stripe_account_id = $guide->stripe_account_id;
                            $failedPayout->payout_id = null;
                            $failedPayout->failure_message = $stripeException->getMessage();
                            $failedPayout->payout_amount = $payoutAmount;
                            $failedPayout->status = 'failed';
                            $failedPayout->month = Carbon::parse($debutMoisDernier)->format('m-Y');
                            $failedPayout->save();
                            Log::channel('stripe_payout')->error('ERROR_PAYOUT | '.$stripeException->getMessage());
                        } 
                        catch(\Exception $e)
                        {
                            Log::channel('stripe_payout')->error('ERROR_PAYOUT | '.$e->getMessage());
                            return false;
                        }
                    }
                }

            }
    }

    public function generateInvoice($guide,$invoiceService,$fileService,$payout, $debut, $fin)
    {
        // 1️⃣ Récupérer le compte connecté
                                $account = Account::retrieve($guide->stripe_account_id);

                                // 2️⃣ Récupérer la liste des comptes bancaires externes
                                $externalAccounts = $account->external_accounts->all([
                                    'object' => 'bank_account'
                                ]);

                                if (count($externalAccounts->data) > 0) {
                                    $bankAccount = $externalAccounts->data[0];  // par exemple le premier
                                    $guide->user->iban = $bankAccount->iban_last4 ?? '';
                                }

                                $url = $invoiceService->generateInvoice($guide->user, $payout->amount, $fileService ,$payout, $debut, $fin);
                                $payout->invoice_url = $url;
                                $payout->save();
    }
    public function deleteStripeConnectAccount($accountId)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        try
        {
            $response = $stripe->accounts->delete($accountId, []);
            Log::channel('stripe_connect')->error('DELETE_CONNECT_ACCOUNT_'.$accountId.' SUCCESS '.json_encode($response));

            return($response->deleted && $response->deleted === true);
        }
        catch(ApiErrorException $stripeException)
        {
            Log::channel('stripe_connect')->error('DELETE_CONNECT_ACCOUNT_'.$accountId.' ERROR '.$stripeException->getMessage());
            return false;
        }
        

    }

}
