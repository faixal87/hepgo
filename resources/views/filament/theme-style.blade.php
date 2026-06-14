@php
    $theme = auth()->user()?->uiTheme() ?? config('hep.ui_themes.polimas_biru_oren');
@endphp

<style>
    :root {
        --hep-sidebar: {{ $theme['sidebar'] }};
        --hep-sidebar-secondary: {{ $theme['sidebar_secondary'] }};
        --hep-accent: {{ $theme['accent'] }};
        --hep-accent-soft: {{ $theme['accent_soft'] }};
        --hep-workspace: {{ $theme['workspace'] }};
    }

    .fi-body {
        background:
            radial-gradient(circle at top left, color-mix(in srgb, var(--hep-accent) 10%, transparent), transparent 28rem),
            var(--hep-workspace);
    }

    .fi-sidebar {
        background: linear-gradient(180deg, var(--hep-sidebar), var(--hep-sidebar-secondary));
        border-right: 0;
    }

    .fi-sidebar .fi-logo,
    .fi-sidebar .fi-sidebar-nav-groups,
    .fi-sidebar .fi-sidebar-group-label,
    .fi-sidebar .fi-sidebar-item-label,
    .fi-sidebar .fi-sidebar-item-icon {
        color: rgb(255 255 255 / 0.88);
    }

    .fi-sidebar .fi-sidebar-item-active,
    .fi-sidebar .fi-sidebar-item-button:hover {
        background: rgb(255 255 255 / 0.13);
    }

    .fi-topbar nav,
    .fi-main-ctn {
        background: rgb(255 255 255 / 0.92);
        backdrop-filter: blur(16px);
    }

    .fi-main {
        background: transparent;
    }
</style>
