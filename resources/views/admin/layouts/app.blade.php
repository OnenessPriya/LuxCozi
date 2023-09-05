<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn-uicons.flaticon.com/uicons-bold-rounded/css/uicons-bold-rounded.css" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <title>{{ config('app.name', 'Lux Cozi') }} | @yield('page')</title>
	
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
                <div class="sidebar-img">
                    <img src="{{ asset('admin/images/logo.png') }}">
                </div>
                <h1>{{ Auth()->guard('admin')->user()->name }}</h1>
                <h4 style=" overflow : hidden ; whitespace: narrow" >{{ Auth()->guard('admin')->user()->email }}</h4>
            </div>
        </div>

        <nav class="main__nav">
            <ul>
				
                <li class="{{ ( request()->is('admin/dashboard*') ) ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i class="fi fi-br-home"></i> <span>Dashboard</span></a></li>

                 {{-- master --}}
                 <li class="@if(request()->is('admin/categories*') || request()->is('admin/collection*') || request()->is('admin/products*')|| request()->is('admin/catalogues*')|| request()->is('admin/schemes*')|| request()->is('admin/states*')|| request()->is('admin/areas*')|| request()->is('admin/colors*')|| request()->is('admin/sizes*')) { {{'active'}} }  @endif">
                    <a href="#"><i class="fi fi-br-cube"></i> <span>Master</span></a>
                    <ul>
                        <li class="{{ ( request()->is('admin/categories*') ) ? 'active' : '' }}"><a href="{{ route('admin.categories.index') }}"><i class="fi fi-br-database"></i> <span>Category</span></a></li>
                        <li class="{{ ( request()->is('admin/collection*') ) ? 'active' : '' }}"><a href="{{ route('admin.collections.index') }}"><i class="fi fi-br-database"></i> <span>Collection</span></a></li>
                        <li class="{{ ( request()->is('admin/products*') ) ? 'active' : '' }}"><a href="{{ route('admin.products.index') }}"><i class="fi fi-br-database"></i><span>Product</span></a></li>
                        <li class="{{ ( request()->is('admin/catalogues*') ) ? 'active' : '' }}"><a href="{{ route('admin.catalogues.index') }}"><i class="fi fi-br-database"></i> <span>Catalogue</span></a></li>
                        <li class="{{ ( request()->is('admin/schemes*') ) ? 'active' : '' }}"><a href="{{ route('admin.schemes.index') }}"><i class="fi fi-br-database"></i> <span>Scheme</span></a></li>
                        <li class="{{ ( request()->is('admin/states*') ) ? 'active' : '' }}"><a href="{{ route('admin.states.index') }}"><i class="fi fi-br-database"></i> <span>State</span></a></li>
                        <li class="{{ ( request()->is('admin/areas*') ) ? 'active' : '' }}"><a href="{{ route('admin.areas.index') }}"><i class="fi fi-br-database"></i> <span>Area</span></a></li>
                        <li class="{{ ( request()->is('admin/color*') ) ? 'active' : '' }}"><a href="{{ route('admin.colors.index') }}"><i class="fi fi-br-database"></i> <span>Color</span></a></li>
                        <li class="{{ ( request()->is('admin/sizes*') ) ? 'active' : '' }}"><a href="{{ route('admin.sizes.index') }}"><i class="fi fi-br-database"></i> <span>Size</span></a></li>
                    </ul>
                </li>
                <li class="@if(request()->is('admin/stores*') ) { {{'active'}} }  @endif">
                    <a href="#"><i class="fi fi-br-cube"></i> <span>Store </span></a>
                    <ul>
                        <li class="{{ ( request()->is('admin/stores*') ) ? 'active' : '' }}"><a href="{{ route('admin.stores.index') }}"><i class="fi fi-br-database"></i> <span>Management</span></a></li>
                        <li class="{{ ( request()->is('admin/stores*') ) ? 'active' : '' }}"><a href="{{ route('admin.stores.noorderreason.index') }}"><i class="fi fi-br-database"></i> <span>No Order Reason List</span></a></li>
                    </ul>
                </li>
                <li class="@if(request()->is('admin/users*') ) { {{'active'}} }  @endif">
                    <a href="#"><i class="fi fi-br-cube"></i> <span>User </span></a>
                    <ul>
                        <li class="{{ ( request()->is('admin/users*') ) ? 'active' : '' }}"><a href="{{ route('admin.users.index') }}"><i class="fi fi-br-database"></i> <span>Management</span></a></li>
                        <li class="{{ ( request()->is('admin/users/activity*') ) ? 'active' : '' }}"><a href="{{ route('admin.users.activity.index') }}"><i class="fi fi-br-database"></i> <span>Activity List</span></a></li>
                        <li class="{{ ( request()->is('admin/users/notification*') ) ? 'active' : '' }}"><a href="{{ route('admin.users.notification.index') }}"><i class="fi fi-br-database"></i> <span>Notification List</span></a></li>
                    </ul>
                </li>
                <li class="@if(request()->is('admin/orders*') ) { {{'active'}} }  @endif">
                    <a href="#"><i class="fi fi-br-cube"></i> <span>Order Report</span></a>
                    <ul>
                        <li class="{{ ( request()->is('admin/orders*') ) ? 'active' : '' }}"><a href="{{ route('admin.orders.index') }}"><i class="fi fi-br-database"></i> <span>Store Wise</span></a></li>
                        <li class="{{ ( request()->is('admin/orders/product*') ) ? 'active' : '' }}"><a href="{{ route('admin.orders.product.index') }}"><i class="fi fi-br-database"></i> <span>Product Wise</span></a></li>
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
                    <div class="dropdown dropdown-header">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>

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
