<script>
    import { page } from '@inertiajs/svelte';
    import { LayoutDashboard, Package, LogOut, Menu, X, ChevronRight } from 'lucide-svelte';
    import { onMount } from 'svelte';

    let isSidebarOpen = true;
    let isMobileMenuOpen = false;

    const toggleSidebar = () => isSidebarOpen = !isSidebarOpen;
    const toggleMobileMenu = () => isMobileMenuOpen = !isMobileMenuOpen;

    const navLinks = [
        { name: 'Dashboard', icon: LayoutDashboard, path: '/' },
        { name: 'Inventory', icon: Package, path: '/inventory' },
    ];
</script>

<div class="min-h-screen bg-[#F8FAFC]">
    <!-- Mobile Sidebar Backdrop -->
    {#if isMobileMenuOpen}
        <button
            type="button"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden w-full h-full border-none cursor-default"
            on:click={toggleMobileMenu}
            aria-label="Close Mobile Menu"
        ></button>
    {/if}

    <!-- Sidebar -->
    <aside
        class="fixed top-0 left-0 h-full bg-white border-r border-slate-200 z-50 transition-all duration-300 ease-in-out
        {isSidebarOpen ? 'w-64' : 'w-20'}
        {isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}"
    >
        <div class="flex flex-col h-full">
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center px-6 border-b border-slate-100">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-lg">iR</span>
                    </div>
                    {#if isSidebarOpen}
                        <span class="font-bold text-slate-800 text-lg whitespace-nowrap">iReply Office</span>
                    {/if}
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
                {#each navLinks as link}
                    <a
                        href={(($page.props?.app_url) || '') + link.path}
                        class="flex items-center gap-3 p-3 rounded-xl transition-all duration-200 group
                        {$page.url === link.path
                            ? 'bg-blue-50 text-blue-600'
                            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'}"
                    >
                        <svelte:component
                            this={link.icon}
                            size={20}
                            class="flex-shrink-0 {$page.url === link.path ? 'text-blue-600' : 'group-hover:text-slate-800'}"
                        />
                        {#if isSidebarOpen}
                            <span class="font-medium text-sm">{link.name}</span>
                        {/if}
                        {#if $page.url === link.path && isSidebarOpen}
                            <ChevronRight size={14} class="ml-auto" />
                        {/if}
                    </a>
                {/each}
            </nav>

            <!-- Bottom Section -->
            <div class="p-3 border-t border-slate-100">
                <button
                    class="w-full flex items-center gap-3 p-3 text-slate-500 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-all duration-200 group"
                >
                    <LogOut size={20} class="flex-shrink-0 group-hover:text-rose-600" />
                    {#if isSidebarOpen}
                        <span class="font-medium text-sm">Logout</span>
                    {/if}
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Shell -->
    <div
        class="transition-all duration-300 ease-in-out min-h-screen
        {isSidebarOpen ? 'lg:pl-64' : 'lg:pl-20'}"
    >
        <!-- Top Header -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 md:px-8">
            <div class="h-full flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button
                        on:click={toggleSidebar}
                        class="hidden lg:flex p-2 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors"
                    >
                        <Menu size={20} />
                    </button>
                    <button
                        on:click={toggleMobileMenu}
                        class="lg:hidden p-2 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors"
                    >
                        <Menu size={20} />
                    </button>
                    <div class="h-6 w-[1px] bg-slate-200 hidden md:block"></div>
                    <h2 class="font-semibold text-slate-800 truncate">
                        {$page.props.auth?.user?.name || 'Back Office Services'}
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-200 border-2 border-white shadow-sm flex items-center justify-center text-slate-600 font-bold text-xs">
                        {($page.props.auth?.user?.name || 'Admin').charAt(0)}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 md:p-8 max-w-7xl mx-auto">
            <slot />
        </main>
    </div>
</div>

<style>
    :global(body) {
        font-family: 'Inter', sans-serif;
    }
</style>
