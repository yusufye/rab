@php
$configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  @if(!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">{{config('variables.templateName')}}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M11.4854 4.88844C11.0081 4.41121 10.2344 4.41121 9.75715 4.88844L4.51028 10.1353C4.03297 10.6126 4.03297 11.3865 4.51028 11.8638L9.75715 17.1107C10.2344 17.5879 11.0081 17.5879 11.4854 17.1107C11.9626 16.6334 11.9626 15.8597 11.4854 15.3824L7.96672 11.8638C7.48942 11.3865 7.48942 10.6126 7.96672 10.1353L11.4854 6.61667C11.9626 6.13943 11.9626 5.36568 11.4854 4.88844Z" fill="currentColor" fill-opacity="0.6" />
        <path d="M15.8683 4.88844L10.6214 10.1353C10.1441 10.6126 10.1441 11.3865 10.6214 11.8638L15.8683 17.1107C16.3455 17.5879 17.1192 17.5879 17.5965 17.1107C18.0737 16.6334 18.0737 15.8597 17.5965 15.3824L14.0778 11.8638C13.6005 11.3865 13.6005 10.6126 14.0778 10.1353L17.5965 6.61667C18.0737 6.13943 18.0737 5.36568 17.5965 4.88844C17.1192 4.41121 16.3455 4.41121 15.8683 4.88844Z" fill="currentColor" fill-opacity="0.38" />
      </svg>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)

    {{-- adding active and open class if child is active --}}

    {{-- menu headers --}}
    @if (isset($menu->menuHeader))
    <li class="menu-header fw-medium mt-4">
      <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
    </li>

    @else

    {{-- active menu method --}}
    @php
      $activeClass = null;
      $currentRouteName =  Route::currentRouteName();

      if ($currentRouteName === $menu->slug) {
          $activeClass = 'active';
      }
      elseif (isset($menu->submenu)) {
        if (gettype($menu->slug) === 'array') {
          foreach($menu->slug as $slug){
            if (str_contains($currentRouteName,$slug) and strpos($currentRouteName,$slug) === 0) {
              $activeClass = 'active open';
            }
          }
        }
        else{
          if (str_contains($currentRouteName,$menu->slug) and strpos($currentRouteName,$menu->slug) === 0) {
            $activeClass = 'active open';
          }
        }

      }

      if (auth()->check()) {
            $permissions = Auth::user()->getAllPermissions();
        } else {
            $permissions = []; 
        }

        $userPermissions = $permissions->pluck('name')->toArray();

       
        $checkSubmenuPermissions = function ($submenu, $userPermissions) use (&$checkSubmenuPermissions) {
            foreach ($submenu as $sub) {
                $submenuPermission = 'read_' . str_replace(' ', '_', strtolower($sub->permission_name));
                
                // Jika submenu ini memiliki permission, return true
                if (in_array($submenuPermission, $userPermissions)) {
                    return true;
                }

                // Jika ada submenu lagi, cek secara rekursif
                if (isset($sub->submenu) && !empty($sub->submenu)) {
                    if ($checkSubmenuPermissions($sub->submenu, $userPermissions)) {
                        return true;
                    }
                }

                
            }
            return false;
        };

        
        $groupPermission = 'read_' . str_replace(' ', '_', strtolower($menu->permission_name));
        $hasGroupPermission = in_array($groupPermission, $userPermissions);
        $hasSubmenu = isset($menu->submenu) && !empty($menu->submenu);
        $hasSubmenuPermission = $hasSubmenu ? $checkSubmenuPermissions($menu->submenu, $userPermissions) : false;
        $shouldDisplayGroup = $hasGroupPermission || $hasSubmenuPermission;
    @endphp

 
    @if ($shouldDisplayGroup)

    {{-- main menu --}}
    <li class="menu-item {{$activeClass}}">
      <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
        @isset($menu->icon)
        <i class="{{ $menu->icon }}"></i>
        @endisset
        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
        @isset($menu->badge)
        <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>

        @endisset
      </a>

      {{-- submenu --}}
      @isset($menu->submenu)
          @php
              $filterAllowedSubmenus = function ($submenu, $userPermissions) use (&$filterAllowedSubmenus) {
                  $filteredSubmenu = [];

                  foreach ($submenu as $sub) {
                      $submenuPermission = 'read_' . str_replace(' ', '_', strtolower($sub->permission_name));

                      // Jika user memiliki permission untuk submenu ini
                      if (in_array($submenuPermission, $userPermissions)) {
                          $sub->submenu = isset($sub->submenu) ? $filterAllowedSubmenus($sub->submenu, $userPermissions) : [];
                          $filteredSubmenu[] = $sub;
                      } elseif (isset($sub->submenu) && !empty($sub->submenu)) {
                          // Jika tidak memiliki izin langsung, cek apakah ada anak dengan izin
                          $sub->submenu = $filterAllowedSubmenus($sub->submenu, $userPermissions);
                          if (!empty($sub->submenu)) {
                              $filteredSubmenu[] = $sub;
                          }
                      }
                  }
                  return $filteredSubmenu;
              };

              $filteredSubmenu = $filterAllowedSubmenus($menu->submenu, $userPermissions);
              @endphp

              @if (!empty($filteredSubmenu))
                @include('layouts.sections.menu.submenu',['menu' => $filteredSubmenu])
              @endif
      @endisset
    </li>
    @endif
     @endif
    @endforeach
  </ul>

</aside>
