<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.svelte';
    import { router, useForm } from '@inertiajs/svelte';
    import {
        Plus,
        Search,
        MoreVertical,
        ArrowUpDown,
        TrendingUp,
        Package,
        X,
        Edit,
        Trash2,
        Layers,
        AlertCircle
    } from 'lucide-svelte';
    import { onMount } from 'svelte';

    export let items = [];
    export let categories = [];
    export let success = null;

    let searchQuery = '';

    $: filteredItems = items.filter(item =>
        item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.sku?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const getStatusColor = (quantity, min) => {
        if (quantity <= 0) return 'bg-rose-100 text-rose-700 border-rose-200';
        if (quantity <= min) return 'bg-amber-100 text-amber-700 border-amber-200';
        return 'bg-emerald-100 text-emerald-700 border-emerald-200';
    };

    const getStatusText = (item) => {
        if (item.status === 'damaged') return 'Damaged';
        if (item.status === 'in_maintenance') return 'In Maintenance';
        
        if (item.quantity <= 0) return 'Out of Stock';
        if (item.quantity <= item.min_stock_level) return 'Low Stock';
        return 'In Stock';
    };

    // States for Modals
    let showItemModal = false;
    let isEditMode = false;
    let showCategoryModal = false;
    let showAdjustModal = false;
    let showDamageModal = false;
    let selectedItem = null;

    // Forms
    const itemForm = useForm({
        id: null,
        category_id: '',
        name: '',
        sku: '',
        quantity: 0,
        unit: 'pcs',
        min_stock_level: 5,
        is_active: true,
    });

    const categoryForm = useForm({
        name: '',
        description: '',
    });

    const adjustForm = useForm({
        item_id: null,
        type: 'in',
        quantity: 1,
        notes: '',
    });

    const damageForm = useForm({
        item_id: null,
        description: '',
    });

    // Handlers
    function openAddItem() {
        isEditMode = false;
        $itemForm.reset();
        $itemForm.clearErrors();
        showItemModal = true;
    }

    function openEditItem(item) {
        isEditMode = true;
        $itemForm.id = item.id;
        $itemForm.category_id = item.category_id;
        $itemForm.name = item.name;
        $itemForm.sku = item.sku;
        $itemForm.quantity = item.quantity;
        $itemForm.unit = item.unit;
        $itemForm.min_stock_level = item.min_stock_level;
        $itemForm.clearErrors();
        showItemModal = true;
    }

    function submitItem() {
        if (isEditMode) {
            $itemForm.put(`/inventory/items/${$itemForm.id}`, {
                onSuccess: () => { showItemModal = false; }
            });
        } else {
            $itemForm.post('/inventory/items', {
                onSuccess: () => { showItemModal = false; }
            });
        }
    }

    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            router.delete(`/inventory/items/${id}`);
        }
    }

    function openAddCategory() {
        $categoryForm.reset();
        $categoryForm.clearErrors();
        showCategoryModal = true;
    }

    function submitCategory() {
        $categoryForm.post('/inventory/categories', {
            onSuccess: () => { showCategoryModal = false; }
        });
    }

    function openAdjustStock(item) {
        $adjustForm.reset();
        $adjustForm.clearErrors();
        $adjustForm.item_id = item.id;
        showAdjustModal = true;
    }

    function submitAdjustStock() {
        $adjustForm.post(`/inventory/items/${$adjustForm.item_id}/adjust`, {
            onSuccess: () => { showAdjustModal = false; }
        });
    }

    function openReportDamage(item) {
        $damageForm.reset();
        $damageForm.item_id = item.id;
        selectedItem = item;
        showDamageModal = true;
    }

    function submitReportDamage() {
        $damageForm.post('/maintenance', {
            onSuccess: () => { showDamageModal = false; }
        });
    }
</script>

<AuthenticatedLayout>
    <div class="space-y-6 relative">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Inventory Management</h1>
                <p class="text-slate-500 mt-1">Manage and track all iReply back office supplies and equipment.</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    on:click={openAddCategory}
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium text-sm hover:bg-slate-50 transition-colors shadow-sm"
                >
                    <Layers size={18} />
                    Add Category
                </button>
                <button
                    on:click={openAddItem}
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 active:scale-95"
                >
                    <Plus size={18} />
                    Add New Item
                </button>
            </div>
        </div>

        {#if success}
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-sm font-medium">{success}</span>
            </div>
        {/if}

        <!-- Filters & Search -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                <input
                    type="text"
                    bind:value={searchQuery}
                    placeholder="Search by name or SKU..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                />
            </div>
            <select class="w-full md:w-48 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="">All Categories</option>
                {#each categories as category}
                    <option value={category.id}>{category.name}</option>
                {/each}
            </select>
            <button class="w-full md:w-auto px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-xl transition-colors font-medium text-sm flex items-center justify-center gap-2">
                <ArrowUpDown size={16} />
                Sort
            </button>
        </div>

        <!-- Status Filters -->
        <div class="flex flex-wrap gap-2">
            <button 
                on:click={() => selectedStatus = 'all'}
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all {selectedStatus === 'all' ? 'bg-slate-800 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'}"
            >
                All Items
            </button>
            <button 
                on:click={() => selectedStatus = 'active'}
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all {selectedStatus === 'active' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-50'}"
            >
                Active
            </button>
            <button 
                on:click={() => selectedStatus = 'inactive'}
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all {selectedStatus === 'inactive' ? 'bg-slate-500 text-white shadow-md' : 'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50'}"
            >
                Inactive
            </button>
            <button 
                on:click={() => selectedStatus = 'rarely_used'}
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all {selectedStatus === 'rarely_used' ? 'bg-amber-500 text-white shadow-md' : 'bg-white text-amber-600 border border-amber-200 hover:bg-amber-50'}"
            >
                Rarely Used
            </button>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden z-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Item Details</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Activity</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Quantity</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each filteredItems as item}
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0 group-hover:bg-white group-hover:shadow-sm transition-all border border-transparent group-hover:border-slate-100">
                                            <Package size={20} />
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{item.name}</p>
                                            <p class="text-xs text-slate-400 font-medium">SKU: {item.sku || 'N/A'}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-semibold border border-slate-200">
                                        {item.category?.name || 'Uncategorized'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 border rounded-full text-[10px] font-bold uppercase tracking-wide {getStatusColor(item)}">
                                        {getStatusText(item)}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {#if item.is_active}
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">ACTIVE</span>
                                    {:else}
                                        <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[10px] font-bold">INACTIVE</span>
                                    {/if}
                                    {#if item.is_rarely_used}
                                        <span class="ml-1 px-2 py-1 bg-amber-100 text-amber-700 rounded text-[10px] font-bold">RARELY USED</span>
                                    {/if}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="font-bold text-slate-800">{item.quantity}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">{item.unit}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button
                                            on:click={() => openAdjustStock(item)}
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Adjust Stock">
                                            <TrendingUp size={18} />
                                        </button>
                                        <button
                                            on:click={() => openEditItem(item)}
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Item">
                                            <Edit size={18} />
                                        </button>
                                        <button
                                            on:click={() => deleteItem(item.id)}
                                            class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Item">
                                            <Trash2 size={18} />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                                            <Package size={24} />
                                        </div>
                                        <div>
                                            <p class="text-slate-500 font-bold">No items found</p>
                                            <p class="text-slate-400 text-sm">Try adjusting your search or add a new item.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                <p>Showing {filteredItems.length} of {items.length} items</p>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1 border border-slate-200 rounded-lg hover:bg-white disabled:opacity-50 transition-all font-bold" disabled>Prev</button>
                    <button class="px-3 py-1 border border-slate-200 rounded-lg hover:bg-white disabled:opacity-50 transition-all font-bold" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Modal (Add/Edit) -->
    {#if showItemModal}
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                <h3 class="font-bold text-lg text-slate-800">{isEditMode ? 'Edit Item' : 'Add New Item'}</h3>
                <button type="button" on:click={() => showItemModal = false} class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-50 transition-colors">
                    <X size={20} />
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <form on:submit|preventDefault={submitItem} class="space-y-4">
                    <div>
                        <label for="item_name" class="block text-sm font-medium text-slate-700 mb-1">Item Name</label>
                        <input id="item_name" type="text" bind:value={$itemForm.name} class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required/>
                        {#if $itemForm.errors?.name}
                            <p class="text-xs text-rose-500 mt-1">{$itemForm.errors.name}</p>
                        {/if}
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="item_sku" class="block text-sm font-medium text-slate-700 mb-1">SKU</label>
                            <input id="item_sku" type="text" bind:value={$itemForm.sku} class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                            {#if $itemForm.errors?.sku}
                                <p class="text-xs text-rose-500 mt-1">{$itemForm.errors.sku}</p>
                            {/if}
                        </div>
                        <div>
                            <label for="item_category" class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                            <select id="item_category" bind:value={$itemForm.category_id} class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white" required>
                                <option value="" disabled>Select category</option>
                                {#each categories as category}
                                    <option value={category.id}>{category.name}</option>
                                {/each}
                            </select>
                            {#if $itemForm.errors?.category_id}
                                <p class="text-xs text-rose-500 mt-1">{$itemForm.errors.category_id}</p>
                            {/if}
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="item_qty" class="block text-sm font-medium text-slate-700 mb-1">Quantity</label>
                            <input id="item_qty" type="number" bind:value={$itemForm.quantity} min="0" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                            {#if $itemForm.errors?.quantity}
                                <p class="text-xs text-rose-500 mt-1">{$itemForm.errors.quantity}</p>
                            {/if}
                        </div>
                        <div>
                            <label for="item_unit" class="block text-sm font-medium text-slate-700 mb-1">Unit</label>
                            <input id="item_unit" type="text" bind:value={$itemForm.unit} placeholder="pcs, kg, etc." class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required/>
                            {#if $itemForm.errors?.unit}
                                <p class="text-xs text-rose-500 mt-1">{$itemForm.errors.unit}</p>
                            {/if}
                        </div>
                        <div>
                            <label for="item_min" class="block text-sm font-medium text-slate-700 mb-1">Min Stock</label>
                            <input id="item_min" type="number" bind:value={$itemForm.min_stock_level} min="0" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                            {#if $itemForm.errors?.min_stock_level}
                                <p class="text-xs text-rose-500 mt-1">{$itemForm.errors.min_stock_level}</p>
                            {/if}
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="is_active" bind:checked={$itemForm.is_active} class="w-5 h-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500 transition-all cursor-pointer"/>
                            <label for="is_active" class="text-sm font-bold text-slate-700 cursor-pointer">Mark as Active Item</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                        <button type="button" on:click={() => showItemModal = false} class="px-5 py-2.5 text-slate-600 font-medium hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" disabled={$itemForm.processing} class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all disabled:opacity-50">
                            {$itemForm.processing ? 'Saving...' : 'Save Item'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {/if}

    <!-- Category Modal -->
    {#if showCategoryModal}
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800">Add Category</h3>
                <button type="button" on:click={() => showCategoryModal = false} class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-50 transition-colors">
                    <X size={20} />
                </button>
            </div>
            <div class="p-6">
                <form on:submit|preventDefault={submitCategory} class="space-y-4">
                    <div>
                        <label for="cat_name" class="block text-sm font-medium text-slate-700 mb-1">Category Name</label>
                        <input id="cat_name" type="text" bind:value={$categoryForm.name} class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required/>
                        {#if $categoryForm.errors?.name}
                            <p class="text-xs text-rose-500 mt-1">{$categoryForm.errors.name}</p>
                        {/if}
                    </div>
                    <div>
                        <label for="cat_desc" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea id="cat_desc" bind:value={$categoryForm.description} rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none"></textarea>
                        {#if $categoryForm.errors?.description}
                            <p class="text-xs text-rose-500 mt-1">{$categoryForm.errors.description}</p>
                        {/if}
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                        <button type="button" on:click={() => showCategoryModal = false} class="px-4 py-2 text-slate-600 font-medium hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" disabled={$categoryForm.processing} class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all disabled:opacity-50">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {/if}

    <!-- Adjust Stock Modal -->
    {#if showAdjustModal}
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800">Adjust Stock</h3>
                <button type="button" on:click={() => showAdjustModal = false} class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-50 transition-colors">
                    <X size={20} />
                </button>
            </div>
            <div class="p-6">
                <form on:submit|preventDefault={submitAdjustStock} class="space-y-4">
                    <div>
                        <label for="adjust_type" class="block text-sm font-medium text-slate-700 mb-1">Adjustment Type</label>
                        <select id="adjust_type" bind:value={$adjustForm.type} class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white" required>
                            <option value="in">Add Stock (In)</option>
                            <option value="out">Remove Stock (Out)</option>
                            <option value="adjustment">Set Exact Count</option>
                        </select>
                    </div>
                    <div>
                        <label for="adjust_quantity" class="block text-sm font-medium text-slate-700 mb-1">Quantity/Amount</label>
                        <input id="adjust_quantity" type="number" bind:value={$adjustForm.quantity} min="0" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required/>
                        {#if $adjustForm.errors?.quantity}
                            <p class="text-xs text-rose-500 mt-1">{$adjustForm.errors.quantity}</p>
                        {/if}
                    </div>
                    <div>
                        <label for="adjust_notes" class="block text-sm font-medium text-slate-700 mb-1">Notes / Reason</label>
                        <textarea id="adjust_notes" bind:value={$adjustForm.notes} rows="2" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                        <button type="button" on:click={() => showAdjustModal = false} class="px-4 py-2 text-slate-600 font-medium hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" disabled={$adjustForm.processing} class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-all disabled:opacity-50">
                            Apply Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {/if}
    <!-- Report Damage Modal -->
    {#if showDamageModal}
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800">Report Damaged Item</h3>
                <button type="button" on:click={() => showDamageModal = false} class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-50 transition-colors">
                    <X size={20} />
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">reporting item</p>
                    <p class="font-bold text-slate-800">{selectedItem?.name}</p>
                </div>
                <form on:submit|preventDefault={submitReportDamage} class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Describe the Issue</label>
                        <textarea 
                            bind:value={$damageForm.description} 
                            rows="4" 
                            class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none" 
                            placeholder="What happened to this item?"
                            required
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                        <button type="button" on:click={() => showDamageModal = false} class="px-4 py-2 text-slate-600 font-medium hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" disabled={$damageForm.processing} class="px-4 py-2 bg-rose-600 text-white font-semibold rounded-xl hover:bg-rose-700 transition-all disabled:opacity-50">
                            Report Damage
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {/if}
</AuthenticatedLayout>
