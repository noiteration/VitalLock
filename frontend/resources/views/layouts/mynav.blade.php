<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">

                    <p class="text-black link-underline-opacity-0">Vital Lock</p>

                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">

                <li class="sidebar-title">
                    <a href="{{route('dashboard')}}">
                        <span>Patient Key Generation (Only Once)</span>
                    </a>
                </li>
                <li class="sidebar-title">
                    <a href="{{route('healthdata.create')}}">
                        <span>Add Patient Comment</span>
                    </a>
                </li>
                <li class="sidebar-title">
                    <a href="{{route('healthdata.index')}}">
                        <span>Check Patient Record</span>
                    </a>
                </li>
                <li class="sidebar-title">
                    <a href="{{route('mto.index')}}">
                        <span>MTO Vision Test Zero Knowledge Proof</span>
                    </a>
                </li>
                <li class="sidebar-title">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                            this.closest('form').submit();"
                            class="sidebar-link">

                            <span style="color:#4264ea">Logout</span>
                        </x-responsive-nav-link>
                    </form>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
