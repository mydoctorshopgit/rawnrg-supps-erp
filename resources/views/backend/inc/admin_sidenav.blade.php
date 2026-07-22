<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <a href="{{ route('admin.dashboard') }}" class="d-block text-left">
                @if(get_setting('system_logo_white') != null)
                    <img class="mw-100" src="{{ uploaded_asset(get_setting('system_logo_white')) }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @else
                    <img class="mw-100" src="{{ static_asset('assets/img/logo.png') }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @endif
            </a>
        </div>
        <div class="aiz-side-nav-wrap">
            <div class="px-3 mb-3 position-relative">
                <input class="form-control bg-transparent rounded-2 form-control-sm text-white fs-14" type="text" name="" placeholder="{{ translate('Search in menu') }}" id="menu-search" onkeyup="menuSearch()">
                <span class="absolute-top-right pr-3 mr-3" style="margin-top: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path id="search_FILL0_wght200_GRAD0_opsz20" d="M176.921-769.231l6.255-6.255a5.99,5.99,0,0,0,1.733.949,5.687,5.687,0,0,0,1.885.329,5.317,5.317,0,0,0,3.9-1.608,5.31,5.31,0,0,0,1.609-3.9,5.322,5.322,0,0,0-1.608-3.9,5.306,5.306,0,0,0-3.9-1.611,5.321,5.321,0,0,0-3.9,1.609,5.312,5.312,0,0,0-1.611,3.9,5.554,5.554,0,0,0,.35,1.946,6.043,6.043,0,0,0,.929,1.672l-6.255,6.255Zm9.874-5.82a4.51,4.51,0,0,1-3.317-1.352,4.51,4.51,0,0,1-1.352-3.317,4.51,4.51,0,0,1,1.352-3.317,4.51,4.51,0,0,1,3.317-1.352,4.51,4.51,0,0,1,3.317,1.352,4.51,4.51,0,0,1,1.352,3.317,4.51,4.51,0,0,1-1.352,3.317A4.51,4.51,0,0,1,186.8-775.051Z" transform="translate(-176.307 785.231)" fill="#4e5767"/>
                    </svg>
                </span>
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                
                {{-- Dashboard --}}
                @can('admin_dashboard')
                    <li class="aiz-side-nav-item">
                        <a href="{{route('admin.dashboard')}}" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                    <path id="_3d6902ec768df53cd9e274ca8a57e401" data-name="3d6902ec768df53cd9e274ca8a57e401" d="M18,12.286a1.715,1.715,0,0,0-1.714-1.714h-4a1.715,1.715,0,0,0-1.714,1.714v4A1.715,1.715,0,0,0,12.286,18h4A1.715,1.715,0,0,0,18,16.286Zm-8.571,0a1.715,1.715,0,0,0-1.714-1.714h-4A1.715,1.715,0,0,0,2,12.286v4A1.715,1.715,0,0,0,3.714,18h4a1.715,1.715,0,0,0,1.714-1.714Zm7.429,0v4a.57.57,0,0,1-.571.571h-4a.57.57,0,0,1-.571-.571v-4a.57.57,0,0,1,.571-.571h4a.57.57,0,0,1,.571.571Zm-8.571,0v4a.57.57,0,0,1-.571.571h-4a.57.57,0,0,1-.571-.571v-4a.57.57,0,0,1,.571-.571h4a.57.57,0,0,1,.571.571ZM9.429,3.714A1.715,1.715,0,0,0,7.714,2h-4A1.715,1.715,0,0,0,2,3.714v4A1.715,1.715,0,0,0,3.714,9.429h4A1.715,1.715,0,0,0,9.429,7.714Zm8.571,0A1.715,1.715,0,0,0,16.286,2h-4a1.715,1.715,0,0,0-1.714,1.714v4a1.715,1.715,0,0,0,1.714,1.714h4A1.715,1.715,0,0,0,18,7.714Zm-9.714,0v4a.57.57,0,0,1-.571.571h-4a.57.57,0,0,1-.571-.571v-4a.57.57,0,0,1,.571-.571h4a.57.57,0,0,1,.571.571Zm8.571,0v4a.57.57,0,0,1-.571.571h-4a.57.57,0,0,1-.571-.571v-4a.57.57,0,0,1,.571-.571h4a.57.57,0,0,1,.571.571Z" transform="translate(-2 -2)" fill="#575b6a" fill-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('Dashboard')}}</span>
                        </a>
                    </li>
                @endcan

                <!-- POS Addon-->
                @if (addon_is_activated('pos_system') && (auth()->user()->can('pos_manager') || auth()->user()->can('pos_configuration')))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13.79" height="16" viewBox="0 0 13.79 16">
                                    <g id="_371925cdd3f531725a9fa8f3ebf8fe9e" data-name="371925cdd3f531725a9fa8f3ebf8fe9e" transform="translate(-2.26 0)">
                                      <path id="Path_40673" data-name="Path 40673" d="M10.69,7H3.26a1.025,1.025,0,0,0-1,1V18.45a1.03,1.03,0,0,0,1,1.05h7.43a1.03,1.03,0,0,0,1.03-1.03V8A1.025,1.025,0,0,0,10.69,7ZM4.94,17.86H3.995v-.95H4.94Zm0-2.355H3.995v-.95H4.94Zm0-2.355H3.995V12.2H4.94Zm2.5,4.71H6.5v-.95h.955Zm0-2.355H6.5v-.95h.955Zm0-2.355H6.5V12.2h.955Zm2.5,4.71H8.99v-.95h.95Zm0-2.355H8.99v-.95h.95Zm0-2.355H8.99V12.2h.95Zm.325-3a.17.17,0,0,1-.165.17H3.835a.17.17,0,0,1-.165-.17V8.795a.165.165,0,0,1,.165-.165H10.13a.165.165,0,0,1,.165.165Zm5.09-1.45H15.13v9.09h.25a.67.67,0,0,0,.67-.67V9.375a.67.67,0,0,0-.695-.675Z" transform="translate(0 -3.5)" fill="#4e5767"/>
                                      <rect id="Rectangle_20842" data-name="Rectangle 20842" width="1.465" height="9.095" transform="translate(12.185 5.2)" fill="#4e5767"/>
                                      <rect id="Rectangle_20843" data-name="Rectangle 20843" width="0.63" height="9.095" transform="translate(14.06 5.2)" fill="#4e5767"/>
                                      <path id="Path_40674" data-name="Path 40674" d="M13.895.895a.89.89,0,0,0-.26-.635A.91.91,0,0,0,13,0a.895.895,0,0,0-.91.895v.53h1.79Zm-2.2,0a.76.76,0,0,1,0-.145.68.68,0,0,1,0-.1h.01A.5.5,0,0,1,11.755.5.43.43,0,0,1,11.79.4a1.2,1.2,0,0,1,.145-.26.5.5,0,0,1,.04-.055L12.045,0H7.995A.815.815,0,0,0,7.18.81V3.03h4.5Z" transform="translate(-2.46)" fill="#4e5767"/>
                                    </g>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('POS System')}}</span>
                            @if (env("DEMO_MODE") == "On")
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.001" viewBox="0 0 16 14.001" class="mx-2">
                                    <path id="Union_49" data-name="Union 49" d="M-19322,3342.5v-5a2.007,2.007,0,0,0-2-2v1.5a3,3,0,0,1-3,3h-4v-10h4a3,3,0,0,1,3,3v1.5a3,3,0,0,1,3,3v5a.506.506,0,0,1-.5.5A.5.5,0,0,1-19322,3342.5Zm-11-2V3339h-3a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v-7.5a.5.5,0,0,1,.5-.5.5.5,0,0,1,.5.5v11a.5.5,0,0,1-.5.5A.506.506,0,0,1-19333,3340.5Zm-3-7.5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v2Z" transform="translate(19337 -3329)" fill="#f51350"/>
                                </svg>
                            @endif
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @can('pos_manager')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('poin-of-sales.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['poin-of-sales.index', 'poin-of-sales.create'])}}">
                                        <span class="aiz-side-nav-text">{{translate('POS Manager')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('pos_configuration')
                                <li class="aiz-side-nav-item d-none">
                                    <a href="{{route('poin-of-sales.activation')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('POS Configuration')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
  <!-- Sale -->
  @canany(['view_all_orders', 'view_inhouse_orders','view_seller_orders','view_pickup_point_orders'])
  <li class="aiz-side-nav-item">
      <a href="#" class="aiz-side-nav-link">
          <div class="aiz-side-nav-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="15.997" height="16" viewBox="0 0 15.997 16">
                  <g id="Layer_2" data-name="Layer 2" transform="translate(-2 -1.994)">
                    <path id="Path_40726" data-name="Path 40726" d="M4.857,12.571H3.714A1.714,1.714,0,0,0,2,14.285V20.57a1.714,1.714,0,0,0,1.714,1.714H4.857A1.714,1.714,0,0,0,6.571,20.57V14.285a1.714,1.714,0,0,0-1.714-1.714Zm.571,8a.571.571,0,0,1-.571.571H3.714a.571.571,0,0,1-.571-.571V14.285a.571.571,0,0,1,.571-.571H4.857a.571.571,0,0,1,.571.571Zm5.142-6.284H9.427A1.714,1.714,0,0,0,7.713,16V20.57a1.714,1.714,0,0,0,1.714,1.714H10.57a1.714,1.714,0,0,0,1.714-1.714V16A1.714,1.714,0,0,0,10.57,14.285Zm.571,6.284a.571.571,0,0,1-.571.571H9.427a.571.571,0,0,1-.571-.571V16a.571.571,0,0,1,.571-.571H10.57a.571.571,0,0,1,.571.571ZM16.283,12H15.14a1.714,1.714,0,0,0-1.714,1.714V20.57a1.714,1.714,0,0,0,1.714,1.714h1.143A1.714,1.714,0,0,0,18,20.57V13.714A1.714,1.714,0,0,0,16.283,12Zm.571,8.57a.571.571,0,0,1-.571.571H15.14a.571.571,0,0,1-.571-.571V13.714a.571.571,0,0,1,.571-.571h1.143a.571.571,0,0,1,.571.571Z" transform="translate(0 -4.289)" fill="#575b6a"/>
                    <path id="Path_40727" data-name="Path 40727" d="M17.947,2.548a.571.571,0,0,0-.366-.24l-1.588-.3a.571.571,0,1,0-.213,1.122l.093.018L11.233,5.932l-5.45-2.18a.572.572,0,1,0-.424,1.062L11.072,7.1a.571.571,0,0,0,.506-.041L16.68,4l-.067.354a.571.571,0,0,0,.457.668.579.579,0,0,0,.107.01.571.571,0,0,0,.56-.465l.3-1.588A.568.568,0,0,0,17.947,2.548Z" transform="translate(-1.286)" fill="#575b6a"/>
                  </g>
              </svg>
          </div>
          <span class="aiz-side-nav-text">Orders</span>
          <span class="aiz-side-nav-arrow"></span>
      </a>
      <!--Submenu-->
      <ul class="aiz-side-nav-list level-2">
          @can('view_all_orders')
              <li class="aiz-side-nav-item">
                  <a href="{{ route('all_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['all_orders.index', 'all_orders.show'])}}">
                      <span class="aiz-side-nav-text">New Orders</span>
                  </a>
              </li>
          @endcan
          @can('view_inhouse_orders')
              <li class="aiz-side-nav-item">
                  <a href="{{ route('pending_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])}}" >
                      <span class="aiz-side-nav-text">Pending Orders</span>
                  </a>
              </li>
          @endcan
             @can('view_inhouse_orders')
              <li class="aiz-side-nav-item">
                  <a href="{{ route('cancelled_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])}}" >
                      <span class="aiz-side-nav-text">Cancelled Orders</span>
                  </a>
              </li>
          @endcan
          @can('view_inhouse_orders')
              <li class="aiz-side-nav-item">
                  <a href="{{ route('fulfillment_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])}}" >
                      <span class="aiz-side-nav-text"> Orders Fulfillment</span>
                  </a>
              </li>
          @endcan
          @can('view_inhouse_orders')
              <li class="aiz-side-nav-item">
                  <a href="{{ route('shipment_order.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])}}" >
                      <span class="aiz-side-nav-text">Shipment Confirmation </span>
                  </a>
              </li>
          @endcan
                                  @can('view_inhouse_orders')
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('international_orders.index') }}"
                                class="aiz-side-nav-link {{ areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])}}">
                                <span class="aiz-side-nav-text">International Orders</span>
                            </a>
                        </li>
                        @endcan
          @if (get_setting('vendor_system_activation') == 1)
              @can('view_seller_orders')
                  <li class="aiz-side-nav-item">
                      <a href="{{ route('seller_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_orders.index', 'seller_orders.show'])}}">
                          <span class="aiz-side-nav-text">{{translate('Seller Orders')}}</span>
                      </a>
                  </li>
              @endcan
          @endif
          
          @can('view_pickup_point_orders')
              {{-- <li class="aiz-side-nav-item">
                  <a href="{{ route('pick_up_point.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['pick_up_point.index','pick_up_point.order_show'])}}">
                      <span class="aiz-side-nav-text">{{translate('Pick-up Point Order')}}</span>
                  </a>
              </li> --}}
          @endcan
      </ul>
  </li>
@endcanany
    <!-- Customers -->
    @canany(['view_all_customers','view_classified_products','view_classified_packages'])
    <li class="aiz-side-nav-item">
        <a href="#" class="aiz-side-nav-link">
            <div class="aiz-side-nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                    <path id="Path_40769" data-name="Path 40769" d="M8,10.667A2.667,2.667,0,1,1,10.667,8,2.667,2.667,0,0,1,8,10.667Zm0-4A1.333,1.333,0,1,0,9.333,8,1.333,1.333,0,0,0,8,6.667Zm4,8.667a4,4,0,1,0-8,0,.667.667,0,0,0,1.333,0,2.667,2.667,0,1,1,5.333,0,.667.667,0,0,0,1.333,0Zm0-10a2.667,2.667,0,1,1,2.667-2.667A2.667,2.667,0,0,1,12,5.333Zm0-4a1.333,1.333,0,1,0,1.333,1.333A1.333,1.333,0,0,0,12,1.333ZM16,10a4,4,0,0,0-4-4,.667.667,0,0,0,0,1.333A2.667,2.667,0,0,1,14.667,10,.667.667,0,1,0,16,10ZM4,5.333A2.667,2.667,0,1,1,6.667,2.667,2.667,2.667,0,0,1,4,5.333Zm0-4A1.333,1.333,0,1,0,5.333,2.667,1.333,1.333,0,0,0,4,1.333ZM1.333,10A2.667,2.667,0,0,1,4,7.333.667.667,0,0,0,4,6a4,4,0,0,0-4,4,.667.667,0,0,0,1.333,0Z" fill="#575b6a"/>
                </svg>
            </div>
            <span class="aiz-side-nav-text">{{ translate('Customers') }}</span>
            <span class="aiz-side-nav-arrow"></span>
        </a>
        <ul class="aiz-side-nav-list level-2">
            @can('view_all_customers')
                <li class="aiz-side-nav-item d-none">
                    <a href="{{ route('commercial_account_index') }}" class="aiz-side-nav-link">
                        <span class="aiz-side-nav-text">{{ translate(' Commercial-Customer list') }}</span>
                    </a>
                </li>
            @endcan
            @if(get_setting('classified_product') == 1)
                {{-- @can('view_classified_products')
                    <li class="aiz-side-nav-item">
                        <a href="{{route('classified_products')}}" class="aiz-side-nav-link">
                            <span class="aiz-side-nav-text">{{translate('Classified Products')}}</span>
                        </a>
                    </li>
                @endcan --}}
                {{-- @can('view_classified_packages')
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('customer_packages.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                            <span class="aiz-side-nav-text">{{ translate('Classified Packages') }}</span>
                        </a>
                    </li>
                @endcan --}}
                @can('view_classified_packages')
                <li class="aiz-side-nav-item">
                    <a href="{{ route('customer_credit.list') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                        <span class="aiz-side-nav-text">{{ translate(' Credit-Customer List') }}</span>
                    </a>
                </li>
                @endcan
                @can('view_classified_packages')
                <li class="aiz-side-nav-item">
                    <a href="{{ route('customer_register.list') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                        <span class="aiz-side-nav-text">{{ translate(' Register-Customer List') }}</span>
                    </a>
                </li>
                @endcan
                @can('view_classified_packages')
                <li class="aiz-side-nav-item">
                    <a href="{{ route('customer_guest.list') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                        <span class="aiz-side-nav-text">{{ translate(' Guest-Customer List') }}</span>
                    </a>
                </li>
                @endcan
                @can('view_classified_packages')
                <li class="aiz-side-nav-item">
                    <a href="{{ route('pharmaceutical_account.list') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                        <span class="aiz-side-nav-text"> Pharmaceutical-Customer List</span>
                    </a>
                </li>
                @endcan
                                        @can('view_classified_packages')
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('international.customer_credit.list') }}"
                                class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('International-Customer List') }}</span>
                            </a>
                        </li>
                        @endcan
            @endif
        </ul>
    </li>
@endcanany
{{-- Accounts Payables --}}
@canany(['view_all_customers','view_classified_products','view_classified_packages'])
<li class="aiz-side-nav-item">
<a href="#" class="aiz-side-nav-link">
<div class="aiz-side-nav-icon">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
<path id="Path_40769" data-name="Path 40769" d="M8,10.667A2.667,2.667,0,1,1,10.667,8,2.667,2.667,0,0,1,8,10.667Zm0-4A1.333,1.333,0,1,0,9.333,8,1.333,1.333,0,0,0,8,6.667Zm4,8.667a4,4,0,1,0-8,0,.667.667,0,0,0,1.333,0,2.667,2.667,0,1,1,5.333,0,.667.667,0,0,0,1.333,0Zm0-10a2.667,2.667,0,1,1,2.667-2.667A2.667,2.667,0,0,1,12,5.333Zm0-4a1.333,1.333,0,1,0,1.333,1.333A1.333,1.333,0,0,0,12,1.333ZM16,10a4,4,0,0,0-4-4,.667.667,0,0,0,0,1.333A2.667,2.667,0,0,1,14.667,10,.667.667,0,1,0,16,10ZM4,5.333A2.667,2.667,0,1,1,6.667,2.667,2.667,2.667,0,0,1,4,5.333Zm0-4A1.333,1.333,0,1,0,5.333,2.667,1.333,1.333,0,0,0,4,1.333ZM1.333,10A2.667,2.667,0,0,1,4,7.333.667.667,0,0,0,4,6a4,4,0,0,0-4,4,.667.667,0,0,0,1.333,0Z" fill="#575b6a"/>
</svg>
</div>
<span class="aiz-side-nav-text">{{ translate('Accounts Payable') }}</span>
<span class="aiz-side-nav-arrow"></span>
</a>
<ul class="aiz-side-nav-list level-2">
@can('view_all_customers')
<li class="aiz-side-nav-item">
<a href="{{ route('invoice_payable') }}" class="aiz-side-nav-link">
    <span class="aiz-side-nav-text">{{ translate('Invoice payable') }}</span>
</a>
</li>
@endcan
@can('view_all_customers')
<li class="aiz-side-nav-item">
<a href="{{ route('payment_exceptions') }}" class="aiz-side-nav-link">
<span class="aiz-side-nav-text">{{ translate('Payments Exceptions') }}</span>
</a>
</li>
@endcan
@can('view_all_customers')
<li class="aiz-side-nav-item">
<a href="{{ route('payment_confirmation') }}" class="aiz-side-nav-link">
<span class="aiz-side-nav-text">{{ translate('Payments Conformation') }}</span>
</a>
</li>
@endcan
@can('view_all_customers')
<li class="aiz-side-nav-item">
<a href="{{ route('payment_overdue') }}" class="aiz-side-nav-link">
<span class="aiz-side-nav-text">{{ translate(' Payments Overdue') }}</span>
</a>
</li>
@endcan
@can('view_all_customers')
<li class="aiz-side-nav-item">
<a href="{{ route('invoice_paid') }}" class="aiz-side-nav-link">
<span class="aiz-side-nav-text">{{ translate('Paid Invoice') }}</span>
</a>
</li>
@endcan

</ul>
</li>
@endcanany
                <!-- Product -->
                @canany(['add_new_product', 'show_all_products','show_in_house_products','show_seller_products','show_digital_products','product_bulk_import','product_bulk_export','view_product_categories', 'view_all_brands','view_product_attributes','view_colors','view_product_reviews'])
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13.714" viewBox="0 0 16 13.714">
                                    <g id="Layer_2" data-name="Layer 2" transform="translate(-2 -4)">
                                      <path id="Path_40719" data-name="Path 40719" d="M17.429,4H2.571A.571.571,0,0,0,2,4.571V8a.571.571,0,0,0,.571.571h.571v8.571a.571.571,0,0,0,.571.571H16.286a.571.571,0,0,0,.571-.571V8.571h.571A.571.571,0,0,0,18,8V4.571A.571.571,0,0,0,17.429,4ZM15.714,16.571H4.286v-8H15.714Zm1.143-9.143H3.143V5.143H16.857Z" fill="#575b6a"/>
                                      <path id="Path_40720" data-name="Path 40720" d="M12.571,15.143H16A.571.571,0,0,0,16,14H12.571a.571.571,0,0,0,0,1.143Z" transform="translate(-4.286 -4.286)" fill="#575b6a"/>
                                    </g>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('Products')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            @can('add_new_product')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('products.create')}}">
                                        <span class="aiz-side-nav-text">{{translate('Add New product')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('show_all_products')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('products.all')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('All Products') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('show_in_house_products')
                                <li class="aiz-side-nav-item d-none">
                                    <a href="{{route('products.admin')}}" class="aiz-side-nav-link {{ areActiveRoutes(['products.admin', 'products.admin.edit']) }}" >
                                        <span class="aiz-side-nav-text">{{ translate('In House Products') }}</span>
                                    </a>
                                </li>
                            @endcan
                           
                           
                            @can('product_bulk_import')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('product_bulk_upload.index') }}" class="aiz-side-nav-link" >
                                        <span class="aiz-side-nav-text">{{ translate('Bulk Import Products') }}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('customer_product_bulk_upload.index') }}" class="aiz-side-nav-link" >
                                        <span class="aiz-side-nav-text">Customer Products Assign</span>
                                    </a>
                                </li>
                            @endcan
                           
                            @can('view_product_categories')
                                <li class="aiz-side-nav-item ">
                                    <a href="{{route('categories.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['categories.index', 'categories.create', 'categories.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Category')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_all_brands')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('brands.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['brands.index', 'brands.create', 'brands.edit'])}}" >
                                        <span class="aiz-side-nav-text">{{translate('Brand')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_product_attributes')
                                <li class="aiz-side-nav-item ">
                                    <a href="{{route('attributes.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['attributes.index','attributes.create','attributes.edit','attributes.show','edit-attribute-value'.''])}}">
                                        <span class="aiz-side-nav-text">{{translate('Attribute')}}</span>
                                    </a>
                                </li>
                            @endcan
                            
                           
                          
                        </ul>
                    </li>
                @endcanany
                @canany(['add_new_product', 'show_all_products','show_in_house_products','show_seller_products','show_digital_products','product_bulk_import','product_bulk_export','view_product_categories', 'view_all_brands','view_product_attributes','view_colors','view_product_reviews'])
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13.714" viewBox="0 0 16 13.714">
                                    <g id="Layer_2" data-name="Layer 2" transform="translate(-2 -4)">
                                      <path id="Path_40719" data-name="Path 40719" d="M17.429,4H2.571A.571.571,0,0,0,2,4.571V8a.571.571,0,0,0,.571.571h.571v8.571a.571.571,0,0,0,.571.571H16.286a.571.571,0,0,0,.571-.571V8.571h.571A.571.571,0,0,0,18,8V4.571A.571.571,0,0,0,17.429,4ZM15.714,16.571H4.286v-8H15.714Zm1.143-9.143H3.143V5.143H16.857Z" fill="#575b6a"/>
                                      <path id="Path_40720" data-name="Path 40720" d="M12.571,15.143H16A.571.571,0,0,0,16,14H12.571a.571.571,0,0,0,0,1.143Z" transform="translate(-4.286 -4.286)" fill="#575b6a"/>
                                    </g>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('Bannars')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            @can('heroBannar.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('heroBannar.index')}}">
                                        <span class="aiz-side-nav-text">Hero Bannars</span>
                                    </a>
                                </li>
                            @endcan
                            @can('middleBannar.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('middleBannar.index')}}">
                                        <span class="aiz-side-nav-text">Middle Bannars</span>
                                    </a>
                                </li>
                            @endcan
                            @can('BestSellerBannar.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{ route('BestSellerBannar.index') }}">
                                        <span class="aiz-side-nav-text">Best Seller Bannars</span>
                                    </a>
                                </li>
                            @endcan
                            @can('trendingBannar.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{ route('trendingBannar.index') }}">
                                        <span class="aiz-side-nav-text">Trending Bannars</span>
                                    </a>
                                </li>
                            @endcan
                            @can('topPickBannar.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{ route('topPickBannar.index') }}">
                                        <span class="aiz-side-nav-text">Top Pick Bannars</span>
                                    </a>
                                </li>
                            @endcan
                            {{-- @can('monthlyBannar.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('monthlyBannar.index')}}">
                                        <span class="aiz-side-nav-text">Monthly Bannars</span>
                                    </a>
                                </li>
                            @endcan
                            @can('lastBannar.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('lastBannar.index')}}">
                                        <span class="aiz-side-nav-text">Last Bannars</span>
                                    </a>
                                </li>
                            @endcan --}}
                            <!-- @can('show_all_products')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('products.all')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('All Products') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('show_in_house_products')
                                <li class="aiz-side-nav-item d-none">
                                    <a href="{{route('products.admin')}}" class="aiz-side-nav-link {{ areActiveRoutes(['products.admin', 'products.admin.edit']) }}" >
                                        <span class="aiz-side-nav-text">{{ translate('In House Products') }}</span>
                                    </a>
                                </li>
                            @endcan
                            -->
                            
                           
                          
                        </ul>
                    </li>
                @endcanany



                {{-- Partners --}}

                @canany(['add_new_product', 'show_all_products','show_in_house_products','show_seller_products','show_digital_products','product_bulk_import','product_bulk_export','view_product_categories', 'view_all_brands','view_product_attributes','view_colors','view_product_reviews'])
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13.714" viewBox="0 0 16 13.714">
                                    <g id="Layer_2" data-name="Layer 2" transform="translate(-2 -4)">
                                      <path id="Path_40719" data-name="Path 40719" d="M17.429,4H2.571A.571.571,0,0,0,2,4.571V8a.571.571,0,0,0,.571.571h.571v8.571a.571.571,0,0,0,.571.571H16.286a.571.571,0,0,0,.571-.571V8.571h.571A.571.571,0,0,0,18,8V4.571A.571.571,0,0,0,17.429,4ZM15.714,16.571H4.286v-8H15.714Zm1.143-9.143H3.143V5.143H16.857Z" fill="#575b6a"/>
                                      <path id="Path_40720" data-name="Path 40720" d="M12.571,15.143H16A.571.571,0,0,0,16,14H12.571a.571.571,0,0,0,0,1.143Z" transform="translate(-4.286 -4.286)" fill="#575b6a"/>
                                    </g>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('Our Partners')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            @can('partners.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('partners.index')}}">
                                        <span class="aiz-side-nav-text">All Partners</span>
                                    </a>
                                </li>
                            @endcan
                            @can('partners.create')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('partners.create')}}">
                                        <span class="aiz-side-nav-text">Add Partners</span>
                                    </a>
                                </li>
                            @endcan
                           
                        
                        </ul>
                    </li>
                @endcanany

                {{-- Blogs --}}
                @canany(['add_new_product', 'show_all_products','show_in_house_products','show_seller_products','show_digital_products','product_bulk_import','product_bulk_export','view_product_categories', 'view_all_brands','view_product_attributes','view_colors','view_product_reviews'])
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13.714" viewBox="0 0 16 13.714">
                                    <g id="Layer_2" data-name="Layer 2" transform="translate(-2 -4)">
                                      <path id="Path_40719" data-name="Path 40719" d="M17.429,4H2.571A.571.571,0,0,0,2,4.571V8a.571.571,0,0,0,.571.571h.571v8.571a.571.571,0,0,0,.571.571H16.286a.571.571,0,0,0,.571-.571V8.571h.571A.571.571,0,0,0,18,8V4.571A.571.571,0,0,0,17.429,4ZM15.714,16.571H4.286v-8H15.714Zm1.143-9.143H3.143V5.143H16.857Z" fill="#575b6a"/>
                                      <path id="Path_40720" data-name="Path 40720" d="M12.571,15.143H16A.571.571,0,0,0,16,14H12.571a.571.571,0,0,0,0,1.143Z" transform="translate(-4.286 -4.286)" fill="#575b6a"/>
                                    </g>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('Blogs')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            @can('blog-category.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('blog-category.index')}}">
                                        <span class="aiz-side-nav-text">Categories</span>
                                    </a>
                                </li>
                            @endcan
                            @can('blog.index')
                                <li class="aiz-side-nav-item">
                                    <a class="aiz-side-nav-link" href="{{route('blog.index')}}">
                                        <span class="aiz-side-nav-text">Blogs</span>
                                    </a>
                                </li>
                            @endcan
                           
                        
                        </ul>
                    </li>
                @endcanany
                
                {{-- Review Section --}}
                @canany(['add_new_product', 'show_all_products','show_in_house_products','show_seller_products','show_digital_products','product_bulk_import','product_bulk_export','view_product_categories', 'view_all_brands','view_product_attributes','view_colors','view_product_reviews'])
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <div class="aiz-side-nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13.714" viewBox="0 0 16 13.714">
                                <g id="Layer_2" data-name="Layer 2" transform="translate(-2 -4)">
                                    <path id="Path_40719" data-name="Path 40719" d="M17.429,4H2.571A.571.571,0,0,0,2,4.571V8a.571.571,0,0,0,.571.571h.571v8.571a.571.571,0,0,0,.571.571H16.286a.571.571,0,0,0,.571-.571V8.571h.571A.571.571,0,0,0,18,8V4.571A.571.571,0,0,0,17.429,4ZM15.714,16.571H4.286v-8H15.714Zm1.143-9.143H3.143V5.143H16.857Z" fill="#575b6a"/>
                                    <path id="Path_40720" data-name="Path 40720" d="M12.571,15.143H16A.571.571,0,0,0,16,14H12.571a.571.571,0,0,0,0,1.143Z" transform="translate(-4.286 -4.286)" fill="#575b6a"/>
                                </g>
                            </svg>
                        </div>
                        <span class="aiz-side-nav-text">{{translate('Client Reviews')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        @can('client-reviews.index')
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="{{route('client-reviews.index')}}">
                                    <span class="aiz-side-nav-text">All Reviews</span>
                                </a>
                            </li>
                        @endcan
                        @can('client-reviews.create')
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="{{route('client-reviews.create')}}">
                                    <span class="aiz-side-nav-text">Add Review</span>
                                </a>
                            </li>
                        @endcan
                        
                    
                    </ul>
                </li>
            @endcanany


             

                <!-- Wholesale Product -->
                @if(addon_is_activated('wholesale'))
                    @canany(['add_wholesale_product','view_all_wholesale_products','view_inhouse_wholesale_products','view_sellers_wholesale_products'])
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <div class="aiz-side-nav-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                        <path id="Union_48" data-name="Union 48" d="M1.2,14.236a1.762,1.762,0,0,1,1.2-1.657V2c0-.325-.268-.823-.6-.823H.6C.268,1.176,0,1.031,0,.7V.647A.645.645,0,0,1,.6,0H2.4A1.407,1.407,0,0,1,3.6,1.41v9.65h10a1.4,1.4,0,0,1,1.2,1.518,1.757,1.757,0,0,1,1.165,2.01,1.8,1.8,0,0,1-3.566-.353,1.761,1.761,0,0,1,1.2-1.656v-.342H3.6v.342a1.754,1.754,0,0,1,1.165,2.01A1.784,1.784,0,0,1,3.338,15.97,1.927,1.927,0,0,1,3,16,1.782,1.782,0,0,1,1.2,14.236Zm12.4,0a.594.594,0,0,0,.6.588h0a.6.6,0,0,0,.6-.589c0-.389-.272-.5-.6-.617C13.872,13.732,13.6,13.846,13.6,14.235Zm-11.2,0a.6.6,0,0,0,.6.588H3a.6.6,0,0,0,.6-.589c0-.389-.272-.5-.6-.617C2.671,13.732,2.4,13.846,2.4,14.235Zm4.216-4.158A1.615,1.615,0,0,1,5,8.462V6.692A1.615,1.615,0,0,1,6.615,5.077h5.77A1.616,1.616,0,0,1,14,6.692V8.462a1.616,1.616,0,0,1-1.616,1.615ZM6.234,6.311a.542.542,0,0,0-.157.382V8.462A.538.538,0,0,0,6.615,9h5.77a.538.538,0,0,0,.538-.538V6.692a.536.536,0,0,0-.538-.538H6.612A.535.535,0,0,0,6.234,6.311ZM5.473,3.527A1.617,1.617,0,0,1,5,2.385V1.616A1.615,1.615,0,0,1,6.615,0H9.384A1.616,1.616,0,0,1,11,1.616v.769A1.615,1.615,0,0,1,9.384,4H6.612A1.614,1.614,0,0,1,5.473,3.527Zm.761-2.293a.542.542,0,0,0-.157.382v.769a.538.538,0,0,0,.538.538H9.384a.538.538,0,0,0,.539-.538V1.616a.542.542,0,0,0-.157-.382.536.536,0,0,0-.382-.157H6.612A.535.535,0,0,0,6.234,1.234Z" fill="#575b6a"/>
                                    </svg>
                                </div>
                                <span class="aiz-side-nav-text">{{translate('Wholesale Products')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.001" viewBox="0 0 16 14.001" class="mx-2">
                                        <path id="Union_49" data-name="Union 49" d="M-19322,3342.5v-5a2.007,2.007,0,0,0-2-2v1.5a3,3,0,0,1-3,3h-4v-10h4a3,3,0,0,1,3,3v1.5a3,3,0,0,1,3,3v5a.506.506,0,0,1-.5.5A.5.5,0,0,1-19322,3342.5Zm-11-2V3339h-3a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v-7.5a.5.5,0,0,1,.5-.5.5.5,0,0,1,.5.5v11a.5.5,0,0,1-.5.5A.506.506,0,0,1-19333,3340.5Zm-3-7.5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v2Z" transform="translate(19337 -3329)" fill="#f51350"/>
                                    </svg>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('add_wholesale_product')
                                    <li class="aiz-side-nav-item">
                                        <a class="aiz-side-nav-link" href="{{route('wholesale_product_create.admin')}}">
                                            <span class="aiz-side-nav-text">{{translate('Add New Wholesale Product')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_all_wholesale_products')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('wholesale_products.all')}}" class="aiz-side-nav-link {{ areActiveRoutes(['wholesale_product_edit.admin']) }}">
                                            <span class="aiz-side-nav-text">{{ translate('All Wholesale Products') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_inhouse_wholesale_products')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('wholesale_products.in_house')}}" class="aiz-side-nav-link {{ areActiveRoutes(['wholesale_product_edit.admin']) }}">
                                            <span class="aiz-side-nav-text">{{ translate('In House Wholesale Products') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @if (get_setting('vendor_system_activation') == 1)
                                    @can('view_sellers_wholesale_products')
                                        <li class="aiz-side-nav-item">
                                            <a href="{{route('wholesale_products.seller')}}" class="aiz-side-nav-link {{ areActiveRoutes(['wholesale_product_edit.admin']) }}">
                                                <span class="aiz-side-nav-text">{{ translate('Seller Wholesale Products') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                            </ul>
                        </li>
                    @endcanany
                @endif

              

                <!-- Deliver Boy Addon-->
                @if (addon_is_activated('delivery_boy'))
                    @canany(['view_all_delivery_boy','add_delivery_boy','delivery_boy_payment_history','collected_histories_from_delivery_boy','order_cancle_request_by_delivery_boy','delivery_boy_configuration'])
                        <li class="aiz-side-nav-item d-none">
                            <a href="#" class="aiz-side-nav-link">
                                <div class="aiz-side-nav-icon">
                                    <svg id="Group_28285" data-name="Group 28285" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                        <path id="Path_40728" data-name="Path 40728" d="M12.406,9.375h-.625v-.84a3.28,3.28,0,0,0,1.406-2.691V4.375h2.344a.469.469,0,0,0,0-.937H13.5a3.594,3.594,0,0,0-7.184.156v.313a.469.469,0,0,0,.313.442v1.5A3.28,3.28,0,0,0,8.031,8.535v.84H7.406a3.605,3.605,0,0,0-2.113.688H1.406a.469.469,0,0,0-.419.259L.049,12.2h0a.466.466,0,0,0-.05.209v3.125A.469.469,0,0,0,.469,16H15.531A.469.469,0,0,0,16,15.531V12.969A3.6,3.6,0,0,0,12.406,9.375ZM9.906.938a2.66,2.66,0,0,1,2.652,2.5h-5.3A2.66,2.66,0,0,1,9.906.938ZM7.562,5.844V4.375H12.25V5.844a2.344,2.344,0,0,1-4.688,0ZM9.906,9.125a3.271,3.271,0,0,0,.938-.137V10a.938.938,0,0,1-1.875,0V8.988A3.27,3.27,0,0,0,9.906,9.125ZM1.7,11H5.554l.469.938h-4.8ZM.937,12.875H6.312v2.188H.937Zm14.125,2.188H7.25V12.406A.466.466,0,0,0,7.2,12.2h0l-.836-1.672a2.638,2.638,0,0,1,1.042-.212h.652a1.875,1.875,0,0,0,3.7,0h.652a2.659,2.659,0,0,1,2.656,2.656Z" fill="#575b6a"/>
                                        <path id="Path_40729" data-name="Path 40729" d="M376.719,405h-1.25a.469.469,0,0,0,0,.938h1.25a.469.469,0,0,0,0-.937Z" transform="translate(-363.281 -392.344)" fill="#575b6a"/>
                                    </svg>
                                </div>
                                <span class="aiz-side-nav-text">{{translate('Delivery Boy')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.001" viewBox="0 0 16 14.001" class="mx-2">
                                        <path id="Union_49" data-name="Union 49" d="M-19322,3342.5v-5a2.007,2.007,0,0,0-2-2v1.5a3,3,0,0,1-3,3h-4v-10h4a3,3,0,0,1,3,3v1.5a3,3,0,0,1,3,3v5a.506.506,0,0,1-.5.5A.5.5,0,0,1-19322,3342.5Zm-11-2V3339h-3a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v-7.5a.5.5,0,0,1,.5-.5.5.5,0,0,1,.5.5v11a.5.5,0,0,1-.5.5A.506.506,0,0,1-19333,3340.5Zm-3-7.5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v2Z" transform="translate(19337 -3329)" fill="#f51350"/>
                                    </svg>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('view_all_delivery_boy')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('delivery-boys.index')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('All Delivery Boy')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('add_delivery_boy')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('delivery-boys.create')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Add Delivery Boy')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('delivery_boy_payment_history')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('delivery-boys-payment-histories')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Payment Histories')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('collected_histories_from_delivery_boy')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('delivery-boys-collection-histories')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Collected Histories')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('order_cancle_request_by_delivery_boy')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('delivery-boy.cancel-request')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Cancel Request')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('delivery_boy_configuration')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('delivery-boy-configuration')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Configuration')}}</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                @endif

                <!-- Refund addon -->
                @if (addon_is_activated('refund_request'))
                    @canany(['view_refund_requests','view_approved_refund_requests','view_rejected_refund_requests','refund_request_configuration'])
                        <li class="aiz-side-nav-item d-none">
                            <a href="#" class="aiz-side-nav-link">
                                <div class="aiz-side-nav-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                        <path id="_4436b8ef9250481406399210799cb7f1" data-name="4436b8ef9250481406399210799cb7f1" d="M19.25,11.25a8.031,8.031,0,0,1-15.995,1,.688.688,0,0,1,1.365-.169A6.643,6.643,0,1,0,7.112,6.039h.866a.686.686,0,1,1,0,1.371H5.384A.687.687,0,0,1,4.7,6.724V4.138a.688.688,0,0,1,1.376,0v.987A8.024,8.024,0,0,1,19.25,11.25ZM11.278,6.907a.687.687,0,0,0-.688.686v.253a2.053,2.053,0,0,0-1.824,2.247,2.146,2.146,0,0,0,2.175,1.842h.8a.686.686,0,1,1,0,1.371h-1.6a.686.686,0,1,0,0,1.371h.458v.229a.688.688,0,0,0,1.376,0v-.26a2.113,2.113,0,0,0,1.824-1.811,2.062,2.062,0,0,0-2.053-2.272h-.917a.686.686,0,1,1,0-1.371h1.609a.686.686,0,1,0,0-1.371h-.462V7.593A.687.687,0,0,0,11.278,6.907Z" transform="translate(-3.25 -3.25)" fill="#575b6a"/>
                                    </svg>
                                </div>
                                <span class="aiz-side-nav-text">{{ translate('Refunds') }}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.001" viewBox="0 0 16 14.001" class="mx-2">
                                        <path id="Union_49" data-name="Union 49" d="M-19322,3342.5v-5a2.007,2.007,0,0,0-2-2v1.5a3,3,0,0,1-3,3h-4v-10h4a3,3,0,0,1,3,3v1.5a3,3,0,0,1,3,3v5a.506.506,0,0,1-.5.5A.5.5,0,0,1-19322,3342.5Zm-11-2V3339h-3a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v-7.5a.5.5,0,0,1,.5-.5.5.5,0,0,1,.5.5v11a.5.5,0,0,1-.5.5A.506.506,0,0,1-19333,3340.5Zm-3-7.5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v2Z" transform="translate(19337 -3329)" fill="#f51350"/>
                                    </svg>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('view_refund_requests')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('refund_requests_all')}}" class="aiz-side-nav-link {{ areActiveRoutes(['refund_requests_all', 'reason_show'])}}">
                                            <span class="aiz-side-nav-text">{{translate('Refund Requests')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_approved_refund_requests')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('paid_refund')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Approved Refunds')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_rejected_refund_requests')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('rejected_refund')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Rejected Refunds')}}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('refund_request_configuration')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('refund_time_config')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Refund Configuration')}}</span>
                                        </a>
                                    </li>
                                @endcan  
                            </ul>
                        </li>
                    @endcanany
                @endif

            
                <!-- Sellers -->
                @if (get_setting('vendor_system_activation') == 1)
                    @canany(['view_all_seller','seller_payment_history','view_seller_payout_requests','seller_commission_configuration','view_all_seller_packages','seller_verification_form_configuration'])
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <div class="aiz-side-nav-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                        <path id="ef567a7fa3ca8f4541f8ab7b62352aa6" d="M19,9.625a.638.638,0,0,0-.079-.307l-2.779-5A.614.614,0,0,0,15.606,4H6.394a.614.614,0,0,0-.536.318l-2.779,5A.638.638,0,0,0,3,9.625a2.5,2.5,0,0,0,1.231,2.153V18.75A1.24,1.24,0,0,0,5.462,20H9.08a1.24,1.24,0,0,0,1.231-1.25V16.058a.759.759,0,0,1,.615-.773.684.684,0,0,1,.534.176.706.706,0,0,1,.229.521V18.75A1.24,1.24,0,0,0,12.92,20h3.618a1.24,1.24,0,0,0,1.231-1.25V11.777A2.5,2.5,0,0,0,19,9.625Zm-1.239.149a1.23,1.23,0,0,1-2.453-.149.578.578,0,0,0-.017-.086.548.548,0,0,0-.006-.084L14.114,5.25h1.132ZM9.164,5.25h1.22V9.625a1.23,1.23,0,0,1-2.455.063Zm2.451,0h1.22l1.235,4.437a1.23,1.23,0,0,1-2.455-.062Zm-4.862,0H7.886l-1.169,4.2a.548.548,0,0,0-.006.084.578.578,0,0,0-.018.086,1.23,1.23,0,0,1-2.453.149Zm9.785,13.5H12.92V15.981a1.964,1.964,0,0,0-.635-1.446,1.9,1.9,0,0,0-1.482-.491A2,2,0,0,0,9.08,16.061V18.75H5.462V12.125a2.439,2.439,0,0,0,1.846-.848A2.419,2.419,0,0,0,11,11.261a2.419,2.419,0,0,0,3.692.016,2.439,2.439,0,0,0,1.846.848Z" transform="translate(-3 -4)" fill="#575b6a"/>
                                    </svg>
                                </div>
                                <span class="aiz-side-nav-text">{{ translate('Sellers') }}</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('view_all_seller')
                                    <li class="aiz-side-nav-item">
                                        @php
                                            $sellers = \App\Models\Shop::where('verification_status', 0)->where('verification_info', '!=', null)->count();
                                        @endphp
                                        <a href="{{ route('sellers.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sellers.index', 'sellers.create', 'sellers.edit', 'sellers.payment_history','sellers.approved','sellers.profile_modal','sellers.show_verification_request'])}}">
                                            <span class="aiz-side-nav-text">{{ translate('All Seller') }}</span>
                                            @if($sellers > 0)<span class="badge badge-info">{{ $sellers }}</span> @endif
                                        </a>
                                    </li>
                                @endcan
                                @can('seller_payment_history')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('sellers.payment_histories') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{ translate('Payouts') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view_seller_payout_requests')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('withdraw_requests_all') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{ translate('Payout Requests') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('seller_commission_configuration')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('business_settings.vendor_commission') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{ translate('Seller Commission') }}</span>
                                        </a>
                                    </li>
                                @endcan
                                @if (addon_is_activated('seller_subscription'))
                                    @can('view_all_seller_packages')
                                        <li class="aiz-side-nav-item">
                                            <a href="{{ route('seller_packages.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_packages.index', 'seller_packages.create', 'seller_packages.edit'])}}">
                                                <span class="aiz-side-nav-text">{{ translate('Seller Packages') }}</span>
                                                @if (env("DEMO_MODE") == "On")
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.001" viewBox="0 0 16 14.001" class="mx-2">
                                                        <path id="Union_49" data-name="Union 49" d="M-19322,3342.5v-5a2.007,2.007,0,0,0-2-2v1.5a3,3,0,0,1-3,3h-4v-10h4a3,3,0,0,1,3,3v1.5a3,3,0,0,1,3,3v5a.506.506,0,0,1-.5.5A.5.5,0,0,1-19322,3342.5Zm-11-2V3339h-3a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v-7.5a.5.5,0,0,1,.5-.5.5.5,0,0,1,.5.5v11a.5.5,0,0,1-.5.5A.506.506,0,0,1-19333,3340.5Zm-3-7.5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h3v2Z" transform="translate(19337 -3329)" fill="#f51350"/>
                                                    </svg>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                                @can('seller_verification_form_configuration')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('seller_verification_form.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{ translate('Seller Verification Form') }}</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                @endif

                {{-- Uploads Files --}}
                <li class="aiz-side-nav-item d-none">
                    <a href="{{ route('uploaded-files.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['uploaded-files.create'])}}">
                        <div class="aiz-side-nav-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                <g id="layer1" transform="translate(-0.53 -0.53)">
                                  <path id="path3159" d="M3.386.53A2.862,2.862,0,0,0,.53,3.386V13.67a2.865,2.865,0,0,0,2.856,2.86H13.67a2.869,2.869,0,0,0,2.86-2.86V3.386A2.865,2.865,0,0,0,13.67.53Zm0,1.143H13.67a1.7,1.7,0,0,1,1.718,1.713V13.67a1.7,1.7,0,0,1-1.718,1.718H3.386A1.7,1.7,0,0,1,1.673,13.67V3.386A1.7,1.7,0,0,1,3.386,1.673ZM8.12,3.557,5.34,6.37a.572.572,0,0,0,0,.809.564.564,0,0,0,.81,0l1.8-1.824V10.8a.571.571,0,0,0,1.143,0V5.347l1.8,1.829a.571.571,0,0,0,.81-.806L8.935,3.557a.511.511,0,0,0-.815,0Zm-4.156,8.97a.571.571,0,0,0,0,1.143h9.128a.571.571,0,0,0,0-1.143Z" fill="#575b6a"/>
                                </g>
                            </svg>
                        </div>
                        <span class="aiz-side-nav-text">{{ translate('Uploaded Files') }}</span>
                    </a>
                </li>

                <!-- Reports -->
                @canany(['in_house_product_sale_report','seller_products_sale_report','products_stock_report','product_wishlist_report','user_search_report','commission_history_report','wallet_transaction_report'])
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg id="stats_3916778" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                    <path id="Path_40739" data-name="Path 40739" d="M16,16H2a2,2,0,0,1-2-2V0H1.333V14A.667.667,0,0,0,2,14.667H16Z" fill="#575b6a"/>
                                    <rect id="Rectangle_21340" data-name="Rectangle 21340" width="1.333" height="6" transform="translate(9.333 7.333)" fill="#575b6a"/>
                                    <rect id="Rectangle_21341" data-name="Rectangle 21341" width="1.333" height="6" transform="translate(4 7.333)" fill="#575b6a"/>
                                    <rect id="Rectangle_21342" data-name="Rectangle 21342" width="1.333" height="9.333" transform="translate(12 4)" fill="#575b6a"/>
                                    <rect id="Rectangle_21343" data-name="Rectangle 21343" width="1.333" height="9.333" transform="translate(6.667 4)" fill="#575b6a"/>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{ translate('Reports') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @can('products_stock_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('stock_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['stock_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Products Stock') }}</span>
                                    </a>
                                </li>
                                  <li class="aiz-side-nav-item">
                                    <a href="{{ route('cancelled_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['stock_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Cancelled Order') }}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('sale_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['stock_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Sale Report') }}</span>
                                    </a>
                                </li>
                            @endcan
                            {{-- @can('in_house_product_sale_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('in_house_sale_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['in_house_sale_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('In House Product Sale') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('seller_products_sale_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('seller_sale_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_sale_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Seller Products Sale') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('products_stock_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('stock_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['stock_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Products Stock') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('product_wishlist_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('wish_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['wish_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Products wishlist') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('user_search_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('user_search_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['user_search_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('User Searches') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('commission_history_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('commission-log.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('Commission History') }}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('wallet_transaction_report')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('wallet-history.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('Wallet Recharge History') }}</span>
                                    </a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endcanany
                

               
             
              

                <!-- Setup & Configurations -->
                @canany(['general_settings','features_activation','language_setup','currency_setup','vat_&_tax_setup',
                        'pickup_point_setup','smtp_settings','payment_methods_configurations','order_configuration','file_system_&_cache_configuration',
                        'social_media_logins','facebook_chat','facebook_comment','analytics_tools_configuration','google_recaptcha_configuration','google_map_setting',
                        'google_firebase_setting','shipping_configuration','shipping_country_setting','manage_shipping_states','manage_shipping_cities','manage_zones','manage_carriers'])
                    <li class="aiz-side-nav-item ">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                    <path id="Path_40779" data-name="Path 40779" d="M7.688,16h.625a1.877,1.877,0,0,0,1.875-1.875V13.81a.209.209,0,0,1,.133-.191l.011,0a.209.209,0,0,1,.23.041l.223.223a1.875,1.875,0,0,0,2.652,0l.442-.442a1.875,1.875,0,0,0,0-2.652l-.223-.223a.209.209,0,0,1-.041-.23l0-.012a.209.209,0,0,1,.191-.133h.315A1.877,1.877,0,0,0,16,8.313V7.688a1.877,1.877,0,0,0-1.875-1.875H13.81a.209.209,0,0,1-.191-.133l0-.011a.209.209,0,0,1,.041-.23l.223-.223a1.875,1.875,0,0,0,0-2.652l-.442-.442a1.875,1.875,0,0,0-2.652,0l-.223.223a.21.21,0,0,1-.23.041l-.012,0a.209.209,0,0,1-.133-.191V1.875A1.877,1.877,0,0,0,8.312,0H7.687A1.877,1.877,0,0,0,5.812,1.875V2.19a.209.209,0,0,1-.133.191l-.012,0a.209.209,0,0,1-.23-.041l-.223-.223a1.875,1.875,0,0,0-2.652,0l-.442.442a1.875,1.875,0,0,0,0,2.652l.223.223a.209.209,0,0,1,.041.23l0,.011a.209.209,0,0,1-.191.133H1.875A1.877,1.877,0,0,0,0,7.687v.625a1.874,1.874,0,0,0,1.407,1.816.625.625,0,1,0,.312-1.211.624.624,0,0,1-.468-.605V7.688a.626.626,0,0,1,.625-.625H2.19a1.455,1.455,0,0,0,1.347-.906l0-.011a1.455,1.455,0,0,0-.312-1.591l-.223-.223a.625.625,0,0,1,0-.884l.442-.442a.625.625,0,0,1,.884,0l.223.223a1.456,1.456,0,0,0,1.593.311l.009,0A1.455,1.455,0,0,0,7.063,2.19V1.875a.626.626,0,0,1,.625-.625h.625a.626.626,0,0,1,.625.625V2.19a1.455,1.455,0,0,0,.906,1.347l.009,0a1.455,1.455,0,0,0,1.593-.311l.223-.223a.625.625,0,0,1,.884,0l.442.442a.625.625,0,0,1,0,.884l-.223.223a1.455,1.455,0,0,0-.311,1.593l0,.009a1.455,1.455,0,0,0,1.347.906h.315a.626.626,0,0,1,.625.625v.625a.626.626,0,0,1-.625.625H13.81a1.455,1.455,0,0,0-1.347.906l0,.009a1.455,1.455,0,0,0,.311,1.593l.223.223a.625.625,0,0,1,0,.884l-.442.442a.625.625,0,0,1-.884,0l-.223-.223a1.456,1.456,0,0,0-1.593-.311l-.009,0a1.455,1.455,0,0,0-.906,1.347v.315a.626.626,0,0,1-.625.625H7.688a.622.622,0,0,1-.6-.437.625.625,0,1,0-1.193.375A1.867,1.867,0,0,0,7.688,16ZM.536,15.433a1.829,1.829,0,0,1,0-2.586h0L4.589,8.811a3.234,3.234,0,0,1-.308-1.259,2.97,2.97,0,0,1,.9-2.141A4.228,4.228,0,0,1,8.13,4.255h.007a3.322,3.322,0,0,1,1.086.188A.625.625,0,0,1,9.47,5.473L7.964,7.01l.188.811L8.95,8,10.479,6.47a.625.625,0,0,1,1.034.24,3.472,3.472,0,0,1,.2,1.121,4.373,4.373,0,0,1-.8,2.556,3.047,3.047,0,0,1-2.49,1.3H8.417A3.414,3.414,0,0,1,7.159,11.4L3.122,15.433a1.829,1.829,0,0,1-2.586,0Zm6.876-5.311a2.1,2.1,0,0,0,1.007.316,1.818,1.818,0,0,0,1.487-.792,2.988,2.988,0,0,0,.528-1.361l-.843.845A.625.625,0,0,1,9.01,9.3L7.494,8.953a.625.625,0,0,1-.471-.468L6.669,6.959a.625.625,0,0,1,.162-.579l.823-.84A2.844,2.844,0,0,0,6.067,6.3,1.723,1.723,0,0,0,5.531,7.55a2.123,2.123,0,0,0,.342,1,.625.625,0,0,1-.065.809L1.419,13.731a.579.579,0,1,0,.819.818l4.368-4.361a.625.625,0,0,1,.806-.066Z" fill="#575b6a"/>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('Setup & Configurations')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            {{-- @can('general_settings')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('general_setting.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('General Settings')}}</span>
                                    </a>
                                </li>
                            @endcan --}}
                            @can('features_activation')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('activation.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Features activation')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('language_setup')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('languages.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['languages.index', 'languages.create', 'languages.store', 'languages.show', 'languages.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Languages')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('currency_setup')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('currency.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Currency')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('vat_&_tax_setup')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('tax.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['tax.index', 'tax.create', 'tax.store', 'tax.show', 'tax.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Vat & TAX')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('pickup_point_setup')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('pick_up_points.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['pick_up_points.index','pick_up_points.create','pick_up_points.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Pickup point')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('smtp_settings')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('smtp_settings.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('SMTP Settings')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('payment_methods_configurations')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('payment_method.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Payment Methods')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('order_configuration')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('order_configuration.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Order Configuration')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('file_system_&_cache_configuration')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('file_system.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('File System & Cache Configuration')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('social_media_logins')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('social_login.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Social media Logins')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @canany(['facebook_chat','facebook_comment'])
                                <li class="aiz-side-nav-item">
                                    <a href="javascript:void(0);" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Facebook')}}</span>
                                        <span class="aiz-side-nav-arrow"></span>
                                    </a>
                                    <ul class="aiz-side-nav-list level-3">
                                        @can('facebook_chat')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('facebook_chat.index') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Facebook Chat')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('facebook_comment')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('facebook-comment') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Facebook Comment')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['analytics_tools_configuration','google_recaptcha_configuration','google_map_setting','google_firebase_setting'])
                                <li class="aiz-side-nav-item">
                                    <a href="javascript:void(0);" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Google')}}</span>
                                        <span class="aiz-side-nav-arrow"></span>
                                    </a>
                                    <ul class="aiz-side-nav-list level-3">
                                        @can('analytics_tools_configuration')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('google_analytics.index') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Analytics Tools')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('google_recaptcha_configuration')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('google_recaptcha.index') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Google reCAPTCHA')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('google_map_setting')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('google-map.index') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Google Map')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('google_firebase_setting')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('google-firebase.index') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Google Firebase')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['shipping_configuration','shipping_country_setting','manage_shipping_states','manage_shipping_cities','manage_zones','manage_carriers'])
                                <li class="aiz-side-nav-item">
                                    <a href="javascript:void(0);" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Shipping')}}</span>
                                        <span class="aiz-side-nav-arrow"></span>
                                    </a>
                                    <ul class="aiz-side-nav-list level-3">
                                        @can('shipping_configuration')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('shipping_configuration.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Shipping Configuration')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('shipping_country_setting')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('countries.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['countries.index','countries.edit','countries.update'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Shipping Countries')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manage_shipping_states')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('states.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['states.index','states.edit','states.update'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Shipping States')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manage_shipping_cities')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('cities.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['cities.index','cities.edit','cities.update'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Shipping Cities')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manage_zones')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('zones.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['zones.index','zones.create','zones.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Shipping Zones')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('manage_carriers')
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('carriers.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['carriers.index','carriers.create','carriers.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Shipping Carrier')}}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endcanany
                @canany(['header_setup','footer_setup','view_all_website_pages','website_appearance','authentication_layout_settings'])
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link {{ areActiveRoutes(['website.footer', 'website.header'])}}" >
                        <div class="aiz-side-nav-icon">
                            <svg id="Group_28315" data-name="Group 28315" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                <circle id="Ellipse_893" data-name="Ellipse 893" cx="0.625" cy="0.625" r="0.625" transform="translate(7.375 6.125)" fill="#575b6a"/>
                                <path id="Path_40777" data-name="Path 40777" d="M13.5,0H2.5A2.5,2.5,0,0,0,0,2.5V11a2.5,2.5,0,0,0,2.5,2.5H7.375v1.25H5.5A.625.625,0,0,0,5.5,16h5a.625.625,0,0,0,0-1.25H8.625V12.875A.625.625,0,0,0,8,12.25H2.5A1.251,1.251,0,0,1,1.25,11V2.5A1.251,1.251,0,0,1,2.5,1.25h11A1.251,1.251,0,0,1,14.75,2.5V11a1.251,1.251,0,0,1-1.25,1.25h-3a.625.625,0,0,0,0,1.25h3A2.5,2.5,0,0,0,16,11V2.5A2.5,2.5,0,0,0,13.5,0Z" fill="#575b6a"/>
                                <path id="Path_40778" data-name="Path 40778" d="M120.375,84.75a.625.625,0,0,0,.625-.625v-.688a3.107,3.107,0,0,0,1.1-.456l.487.487a.625.625,0,0,0,.884-.884l-.487-.487a3.108,3.108,0,0,0,.456-1.1h.688a.625.625,0,1,0,0-1.25h-.688a3.108,3.108,0,0,0-.456-1.1l.487-.487a.625.625,0,0,0-.884-.884l-.487.487a3.107,3.107,0,0,0-1.1-.456v-.688a.625.625,0,0,0-1.25,0v.688a3.108,3.108,0,0,0-1.1.456l-.487-.487a.625.625,0,0,0-.884.884l.487.487a3.108,3.108,0,0,0-.456,1.1h-.688a.625.625,0,0,0,0,1.25h.688a3.108,3.108,0,0,0,.456,1.1l-.487.487a.625.625,0,0,0,.884.884l.487-.487a3.107,3.107,0,0,0,1.1.456v.688A.625.625,0,0,0,120.375,84.75ZM118.5,80.375a1.875,1.875,0,1,1,1.875,1.875A1.877,1.877,0,0,1,118.5,80.375Z" transform="translate(-112.375 -73.625)" fill="#575b6a"/>
                            </svg>
                        </div>
                        <span class="aiz-side-nav-text">{{translate('Website Setup')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        @can('select_homepage')
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.select-homepage') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Select Homepage')}}</span>
                                </a>
                            </li>
                        @endcan
                        @can('edit_website_page')
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('custom-pages.edit', ['id'=>'home', 'lang'=>env('DEFAULT_LANGUAGE'), 'page'=>'home']) }}" 
                                    class="aiz-side-nav-link {{ (url()->current() == url('/admin/website/custom-pages/edit/home')) ? 'active' : '' }}">
                                    <span class="aiz-side-nav-text">{{translate('Homepage Settings')}}</span>
                                </a>
                            </li>
                        @endcan
                        @can('authentication_layout_settings')
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.authentication-layout-settings') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Authentication Layout & Settings')}}</span>
                                </a>
                            </li>
                        @endcan
                        @can('header_setup')
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.header') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Header')}}</span>
                                </a>
                            </li>
                        @endcan
                        @can('footer_setup')
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.footer', ['lang'=>  App::getLocale()] ) }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.footer'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Footer')}}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view_all_website_pages')
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.pages') }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.pages', 'custom-pages.create' ,'custom-pages.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Pages')}}</span>
                                </a>
                            </li>
                        @endcan
                        @can('website_appearance')
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.appearance') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Appearance')}}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
                <!-- Staffs -->
                @canany(['view_all_staffs','view_staff_roles'])
                    <li class="aiz-side-nav-item d-none">
                        <a href="#" class="aiz-side-nav-link">
                            <div class="aiz-side-nav-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                    <g id="Group_28314" data-name="Group 28314" transform="translate(-19299 2175)">
                                      <path id="Path_40774" data-name="Path 40774" d="M87.867,3.07H84.133V1.72A.716.716,0,0,0,83.422,1H80.578a.716.716,0,0,0-.711.72V3.07H76.133A2.149,2.149,0,0,0,74,5.229V14.84A2.149,2.149,0,0,0,76.133,17H87.867A2.149,2.149,0,0,0,90,14.84V5.229A2.149,2.149,0,0,0,87.867,3.07Zm-6.578-.63h1.422V3.79a.711.711,0,1,1-1.422,0Zm7.289,12.4a.716.716,0,0,1-.711.72H76.133a.716.716,0,0,1-.711-.72V5.229a.716.716,0,0,1,.711-.72h3.856a2.124,2.124,0,0,0,4.022,0h3.856a.716.716,0,0,1,.711.72Z" transform="translate(19225 -2176)" fill="#575b6a"/>
                                      <g id="Group_28312" data-name="Group 28312" transform="translate(19305.07 -2169.197)">
                                        <path id="Path_40775" data-name="Path 40775" d="M199.864,197.932a1.932,1.932,0,1,0-1.932,1.932A1.934,1.934,0,0,0,199.864,197.932Zm-1.932.644a.644.644,0,1,1,.644-.644A.645.645,0,0,1,197.932,198.576Z" transform="translate(-196 -196)" fill="#575b6a"/>
                                      </g>
                                      <g id="Group_28313" data-name="Group 28313" transform="translate(19303.779 -2165)">
                                        <path id="Path_40776" data-name="Path 40776" d="M160.508,316h-2.576A1.934,1.934,0,0,0,156,317.932v1.288a.644.644,0,1,0,1.288,0v-1.288a.645.645,0,0,1,.644-.644h2.576a.645.645,0,0,1,.644.644v1.288a.644.644,0,1,0,1.288,0v-1.288A1.934,1.934,0,0,0,160.508,316Z" transform="translate(-156 -316)" fill="#575b6a"/>
                                      </g>
                                    </g>
                                </svg>
                            </div>
                            <span class="aiz-side-nav-text">{{translate('Staffs')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @can('view_all_staffs')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('staffs.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('All staffs')}}</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_staff_roles')
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('roles.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['roles.index', 'roles.create', 'roles.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Staff permissions')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

              

               
            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->
