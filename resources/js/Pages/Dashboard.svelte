<script>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import {
        Package,
        AlertTriangle,
        ArrowUpRight,
        ArrowDownRight,
        Layers,
    } from "lucide-svelte";

    export let auth = {};
    export let stats = {
        total_items: 0,
        low_stock: 0,
        total_categories: 0,
    };
    $: displayStats = [
        {
            name: "Total Items",
            value: stats.total_items,
            icon: Package,
            color: "text-blue-600",
            bg: "bg-blue-50",
        },
        {
            name: "Low Stock",
            value: stats.low_stock,
            icon: AlertTriangle,
            color: "text-amber-600",
            bg: "bg-amber-50",
        },
        {
            name: "Categories",
            value: stats.total_categories,
            icon: Layers,
            color: "text-indigo-600",
            bg: "bg-indigo-50",
        },
    ];
</script>

<AuthenticatedLayout>
    <div class="space-y-8">
        <!-- Welcome Section -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Dashboard Overview
                </h1>
                <p class="text-slate-500 mt-1">
                    Welcome back, {auth.user?.name || "Administrator"}. Here's
                    what's happening today.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a
                    href="/report"
                    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium text-sm hover:bg-slate-50 transition-colors shadow-sm"
                >
                    Generate Report
                </a>
                <button
                    class="px-4 py-2 bg-blue-600 text-white rounded-xl font-medium text-sm hover:bg-blue-700 transition-colors shadow-md"
                >
                    + Add New Item
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {#each displayStats as stat}
                <div
                    class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                {stat.name}
                            </p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-2">
                                {stat.value}
                            </h3>
                        </div>
                        <div class="{stat.bg} {stat.color} p-3 rounded-xl">
                            <svelte:component this={stat.icon} size={24} />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span
                            class="flex items-center text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-full"
                        >
                            <ArrowUpRight size={12} class="mr-1" />
                            +0%
                        </span>
                        <span class="text-slate-400 text-xs italic"
                            >from last month</span
                        >
                    </div>
                </div>
            {/each}
        </div>

        <!-- Recent Activity & Charts Placeholder -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Items Table Preview -->
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
            >
                <div
                    class="p-6 border-b border-slate-100 flex items-center justify-between"
                >
                    <h3 class="font-bold text-slate-800">Recent Items</h3>
                    <a
                        href="/inventory"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                        >View All</a
                    >
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th
                                    class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider"
                                    >Item Name</th
                                >
                                <th
                                    class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider"
                                    >Status</th
                                >
                                <th
                                    class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right"
                                    >Stock</th
                                >
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-slate-100 italic text-slate-400 text-sm"
                        >
                            <tr>
                                <td
                                    colspan="3"
                                    class="px-6 py-8 text-center bg-slate-50/30"
                                >
                                    No recent items to display.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stock Movement Preview -->
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"
            >
                <h3 class="font-bold text-slate-800 mb-6">
                    Stock Movement (7 days)
                </h3>
                <div
                    class="h-48 bg-slate-50 rounded-xl border border-dashed border-slate-200 flex items-center justify-center"
                >
                    <p class="text-slate-400 font-medium text-sm">
                        Chart will be displayed here
                    </p>
                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
