<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
         <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon text-warning" data-feather="star"></i></a>
                    <div class="bookmark-input search-input">
                        <div class="bookmark-input-icon"><i data-feather="search"></i></div>
                        <input class="form-control input" type="text" placeholder="Bookmark" tabindex="0" data-search="search">
                        <ul class="search-list search-list-bookmark"></ul>
                    </div>
                </li>
            </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">
{{--            <li class="nav-item dropdown dropdown-language"><a class="nav-link dropdown-toggle" id="dropdown-flag" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="flag-icon flag-icon-us"></i><span class="selected-language">English</span></a>--}}
{{--                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-flag"><a class="dropdown-item" href="#" data-language="en"><i class="flag-icon flag-icon-us"></i> English</a><a class="dropdown-item" href="#" data-language="eg"><i class="flag-icon flag-icon-eg"></i> Egypt</a></div>--}}
{{--            </li>--}}

            <li class="nav-item dropdown dropdown-language">
                <a class="nav-link dropdown-toggle" id="dropdown-flag" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
{{--                    <i class="flag-icon flag-icon-{{ app()->getLocale() }}"></i>--}}
                    <span class="selected-language">{{ ucfirst(LaravelLocalization::getCurrentLocaleNative()) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-flag">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('locale-form-{{ $localeCode }}').submit();">
                            <i class="flag-icon flag-icon-{{ $properties['regional'] }}"></i>
                            {{ ucfirst($properties['native']) }}
                        </a>
                        <form id="locale-form-{{ $localeCode }}" action="{{ LaravelLocalization::getLocalizedURL($localeCode) }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="GET">
                        </form>
                    @endforeach
                </div>
            </li>
            <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a></li>

            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">{{ Auth::user()->name }}</span>
                        <span class="user-status">{{{Auth::user()->getRoleNames()->first()}}}</span>
                    </div>
                    <span class="avatar">
                        <img class="round" src="../../../app-assets/images/portrait/small/avatar-s-11.jpg" alt="avatar" height="40" width="40">
                        <span class="avatar-status-online">
                        </span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="me-50" data-feather="user"></i> Profile</a>
                    <a class="dropdown-item" href="#"
                      onclick="$('#logout_form').submit()">
                        <i class="me-50" data-feather="power"></i> {{ __('Log Out') }}
                    </a>
                    <form id="logout_form" method="POST" action="{{ route('logout') }}" style="display: none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
