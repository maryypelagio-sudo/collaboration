<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.svelte';
    import { router } from '@inertiajs/svelte';
    import { HandHelping, History, Clock, User, Package, CheckCircle2, XCircle } from 'lucide-svelte';

    export let borrowings = [];
    export let items = [];

    let showModal = false;
    let selectedItem = '';
    let quantity = 1;
    let notes = '';

    const submitBorrowing = () => {
        router.post('/borrowings', {
            item_id: selectedItem,
            quantity: quantity,
            notes: notes
        }, {
            onSuccess: () => {
                showModal = false;
                selectedItem = '';
                quantity = 1;
                notes = '';
            }
        });
    };

    const returnItem = (id) => {
        router.post(`/borrowings/${id}/return`);
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).format(new Date(dateString));
    };
</script>

<AuthenticatedLayout>
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Borrowed Equipment</h1>
                <p class="text-slate-500 mt-1">Track and manage equipment currently borrowed by team members.</p>
            </div>
            <button 
                on:click={() => showModal = true}
                class="px-4 py-2 bg-blue-600 text-white rounded-xl font-medium text-sm hover:bg-blue-700 transition-colors shadow-md flex items-center gap-2"
            >
                <HandHelping size={18} />
                Record Borrowing
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Equipment</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Borrowed By</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each borrowings as borrowing}
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                            <Package size={20} />
                                        </div>
                                        <div>
                                            <a href="/inventory?search={borrowing.item.name}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors">{borrowing.item.name}</a>
                                            <div class="text-xs text-slate-500">Qty: {borrowing.quantity}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-500">
                                            {borrowing.user.name.charAt(0)}
                                        </div>
                                        {borrowing.user.name}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 italic">
                                    <div>{formatDate(borrowing.borrowed_at)}</div>
                                    {#if borrowing.returned_at}
                                        <div class="text-[10px] text-emerald-600 font-medium">Returned: {formatDate(borrowing.returned_at)}</div>
                                    {/if}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide
                                        {borrowing.status === 'borrowed' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600'}">
                                        {borrowing.status}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {#if borrowing.status === 'borrowed'}
                                        <button 
                                            on:click={() => returnItem(borrowing.id)}
                                            class="text-blue-600 hover:text-blue-700 font-semibold text-sm transition-colors"
                                        >
                                            Return Item
                                        </button>
                                    {:else}
                                        <CheckCircle2 size={18} class="text-slate-300 ml-auto" />
                                    {/if}
                                </td>
                            </tr>
                        {/each}
                        {#if borrowings.length === 0}
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                                    No borrowings recorded yet.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {#if showModal}
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-900">Record Borrowing</h3>
                    <button on:click={() => showModal = false} class="text-slate-400 hover:text-slate-600 transition-colors">
                        <XCircle size={20} />
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Equipment</label>
                        <select 
                            bind:value={selectedItem}
                            class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        >
                            <option value="">Select an item</option>
                            {#each items as item}
                                <option value={item.id}>{item.name} ({item.quantity} available)</option>
                            {/each}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Quantity</label>
                        <input 
                            type="number" 
                            bind:value={quantity}
                            min="1"
                            class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Notes (Optional)</label>
                        <textarea 
                            bind:value={notes}
                            rows="3"
                            class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Reason for borrowing..."
                        ></textarea>
                    </div>
                </div>
                <div class="p-6 bg-slate-50/50 flex gap-3">
                    <button 
                        on:click={() => showModal = false}
                        class="flex-1 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        on:click={submitBorrowing}
                        disabled={!selectedItem}
                        class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 shadow-md shadow-blue-200"
                    >
                        Save Recording
                    </button>
                </div>
            </div>
        </div>
    {/if}
</AuthenticatedLayout>
