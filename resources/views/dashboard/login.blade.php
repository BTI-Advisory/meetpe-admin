<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    MeetPe
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->

  @vite('resources/assets/css/nucleo-icons.css')
  @vite('resources/assets/css/nucleo-svg.css')
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  @vite('resources/assets/css/argon-dashboard.css')
  <style>
    .form-switch .form-check-input:checked {
    border-color:#FF4C00!important;
    background-color:#FF4C00!important;
}
  </style>
</head>

<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
          <div class="container-fluid">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" style="color: #FF4C00!important">
                MeetPe
            </a>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
              <div class="card card-plain">
                <img src="{{asset("img/logo-ct-dark.png")}}" alt="">
                <div class="card-header pb-0 text-start">
                  <p class="mb-0">Entrez votre email et votre mot de passe pour vous connecter</p>
                </div>
                <div class="card-body">
                  <form role="form">
                    <div class="mb-3">
                      <input type="email" class="form-control form-control-lg" placeholder="Email" aria-label="Email">
                    </div>
                    <div class="mb-3">
                      <input type="email" class="form-control form-control-lg" placeholder="Mot de pass" aria-label="Password">
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input"  type="checkbox" id="rememberMe">
                      <label class="form-check-label" for="rememberMe">Souviens-toi de moi</label>
                    </div>
                    <div class="text-center">
                      <button type="button" class="btn btn-lg text-white  btn-lg w-100 mt-4 mb-0" style="background-color: #FF4C00!important">se connecter</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
              <div class="position-relative  h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-color: #ff4d0082">
                <img src="" alt="" style="background-size:cover;!important;">

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!--   Core JS Files   -->
  @vite(['resources/assets/js/plugins/chartjs.min.js', 'resources/assets/js/plugins/smooth-scrollbar.min.js',
  'resources/assets/js/plugins/perfect-scrollbar.min.js','resources/assets/js/core/bootstrap.min.js',
  'resources/assets/js/argon-dashboard.min.js','resources/assets/js/core/popper.min.js'])
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.0.4"></script>
</body>

</html>
