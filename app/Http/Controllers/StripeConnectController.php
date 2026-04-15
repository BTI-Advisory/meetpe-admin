<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;

class StripeConnectController extends Controller
{
    public function __construct(private StripeService $stripeService) {}

    /**
     * Stripe redirige ici quand le lien d'onboarding a expiré.
     * On regénère un nouveau lien et on redirige le guide.
     */
    public function refresh(string $accountId)
    {
        Log::channel('stripe_connect')->info('REFRESH_LINK_REQUESTED_FOR_ACCOUNT_' . $accountId);

        $guide = Guide::where('stripe_account_id', $accountId)->first();

        if (! $guide) {
            Log::channel('stripe_connect')->error('REFRESH_LINK_FAILED_ACCOUNT_NOT_FOUND_' . $accountId);
            abort(404);
        }

        $url = $this->stripeService->generateAccountLink($accountId);

        if (! $url) {
            Log::channel('stripe_connect')->error('REFRESH_LINK_GENERATION_FAILED_FOR_ACCOUNT_' . $accountId);
            abort(500, 'Impossible de générer un nouveau lien Stripe.');
        }

        $guide->stripe_connect_form_url = $url;
        $guide->save();

        Log::channel('stripe_connect')->info('REFRESH_LINK_GENERATED_FOR_ACCOUNT_' . $accountId);

        return redirect($url);
    }

    /**
     * Stripe redirige ici après que le guide a complété (ou quitté) l'onboarding.
     */
    public function return(string $accountId)
    {
        Log::channel('stripe_connect')->info('RETURN_FROM_ONBOARDING_FOR_ACCOUNT_' . $accountId);

        return view('stripe.connect-return', ['accountId' => $accountId]);
    }
}
