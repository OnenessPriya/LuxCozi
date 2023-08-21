<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn-uicons.flaticon.com/uicons-bold-rounded/css/uicons-bold-rounded.css" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

    <title>{{ config('app.name', 'OnnB2B') }} | @yield('page')</title>
	
	<style>
		.page-item.active .page-link {
			background-color: #d20a0e;
			border-color: #d20a0e;
		}
		.page-link, .page-link:hover, .page-link:focus {
			color: #d20a0e;
			box-shadow: none;
		}
	</style>
</head>

<body>
    <aside class="side__bar shadow-sm">
        <div class="admin__logo">
            <div class="logo">
                
            </div>
            <div class="admin__info" style="width: 100% ; overflow : hidden" >
                <h1>{{ Auth()->guard('admin')->user()->name }}</h1>
                <h4 style=" overflow : hidden ; whitespace: narrow" >{{ Auth()->guard('admin')->user()->email }}</h4>
            </div>
        </div>

        <nav class="main__nav">
            <ul>
				
                <li class="{{ ( request()->is('admin/dashboard*') ) ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i class="fi fi-br-home"></i> <span>Dashboard</span></a></li>

                
                    {{-- product --}}
                <li class="@if(request()->is('admin/product*') || request()->is('admin/faq*')) { {{'active'}} }  @endif">
                    <a href="#"><i class="fi fi-br-cube"></i> <span>Product</span></a>
                    <ul>
                        <li class="{{ ( request()->is('admin/products*') ) ? 'active' : '' }}"><a href="{{ route('admin.products.index') }}">All Product</a></li>

                        <li class="{{ ( request()->is('admin/product/create*') ) ? 'active' : '' }}"><a href="{{ route('admin.products.create') }}">Add New</a></li>
                    </ul>
                </li>
				{{-- reward app --}}
              
				
				
            </ul>
        </nav>
         <div class="nav__footer">
            <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fi fi-br-cube"></i> <span>Log Out</span></a>
        </div>
    </aside>
    <main class="admin">
       <header>
            <div class="row align-items-center">
                <div class="col-auto ms-auto">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::guard('admin')->user()->name }}
                        </button>
                        
                    </div>
                </div>
            </div>
        </header>
        <section class="admin__title">
            <h1>@yield('page')</h1>
        </section>

        @yield('content')

        <footer>
            <div class="row">
                <div class="col-12 text-end">Lux Innerwear 2023-2024</div>
            </div>
        </footer>
    </main>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>
    <script type="text/javascript" src="{{ asset('admin/js/custom.js') }}"></script>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    {{-- <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.22/sweetalert2.min.js"></script> --}}

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
		// tooltip
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		  return new bootstrap.Tooltip(tooltipTriggerEl)
		})

        // click to select all checkbox
        function headerCheckFunc() {
            if ($('#flexCheckDefault').is(':checked')) {
                $('.tap-to-delete').prop('checked', true);
                clickToRemove();
            } else {
                $('.tap-to-delete').prop('checked', false);
                clickToRemove();
            }
        }

        // sweetalert fires | type = success, error, warning, info, question
        function toastFire(type = 'success', title, body = '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                showCloseButton: true,
                timer: 2000,
                timerProgressBar: false,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: type,
                title: title,
                // text: body
            })
        }

        // on session toast fires
        @if (Session::get('success'))
            toastFire('success', '{{ Session::get('success') }}');
        @elseif (Session::get('failure'))
            toastFire('warning', '{{ Session::get('failure') }}');
        @endif
    </script>

    @yield('script')
</body>
</html>
