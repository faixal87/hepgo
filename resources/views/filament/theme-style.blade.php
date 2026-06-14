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

    .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
    .fi-sidebar .fi-sidebar-item-btn:hover {
        background: rgb(255 255 255 / 0.13);
    }

    .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background: rgb(255 255 255 / 0.96);
    }

    .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label,
    .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon {
        color: rgb(15 23 42 / 0.92);
    }

    .fi-topbar nav,
    .fi-main-ctn {
        background: rgb(255 255 255 / 0.92);
        backdrop-filter: blur(16px);
    }

    .fi-main {
        background: transparent;
    }

    .hep-topbar-tools {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-inline-end: 0.75rem;
    }

    .hep-topbar-portal {
        display: inline-grid;
        height: 2.8rem;
        width: 2.8rem;
        place-items: center;
        border-radius: 9999px;
        border: 1px solid color-mix(in srgb, var(--hep-accent) 22%, white);
        background: linear-gradient(135deg, var(--hep-accent-soft), rgb(255 255 255 / 0.98));
        color: var(--hep-accent);
        box-shadow: 0 10px 24px color-mix(in srgb, var(--hep-accent) 14%, transparent);
        transition: all 160ms ease;
    }

    .hep-topbar-portal:hover {
        border-color: var(--hep-accent);
        background: linear-gradient(135deg, var(--hep-accent), color-mix(in srgb, var(--hep-accent) 82%, #f97316));
        color: white;
        transform: translateY(-1px);
    }

    .hep-user-summary {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        background: rgb(248 250 252 / 0.96);
        padding: 0.42rem 0.8rem;
        max-width: 15rem;
    }

    .hep-user-summary-text {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        line-height: 1.15;
    }

    .hep-user-summary-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: rgb(71 85 105 / 0.88);
    }

    .hep-user-summary-name {
        font-size: 0.94rem;
        font-weight: 800;
        color: rgb(15 23 42 / 0.95);
        max-width: 14rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fi-topbar .fi-user-menu-trigger {
        padding: 0.15rem;
    }

    .fi-topbar .fi-user-avatar {
        height: 3rem;
        width: 3rem;
        border: 2px solid rgb(255 255 255 / 0.96);
        box-shadow: 0 10px 24px rgb(15 23 42 / 0.12);
    }

    @media (max-width: 768px) {
        .hep-topbar-tools {
            gap: 0.5rem;
            margin-inline-end: 0.25rem;
        }

        .hep-user-summary {
            display: none;
        }

        .fi-topbar .fi-user-avatar {
            height: 2.75rem;
            width: 2.75rem;
        }
    }
</style>
