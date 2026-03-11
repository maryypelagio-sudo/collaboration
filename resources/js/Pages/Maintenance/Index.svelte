<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.svelte';
    import { useForm, router } from '@inertiajs/svelte';
    import { 
        Wrench, 
        Clock, 
        CheckCircle2, 
        XCircle, 
        AlertCircle,
        User,
        Calendar,
        Package,
        MoreVertical,
        ChevronRight
    } from 'lucide-svelte';

    export let logs = [];
    export let items = [];
    export let success = null;

    const statusMap = {
        pending: { label: 'Pending', color: 'bg-slate-100 text-slate-700 border-slate-200', icon: Clock },
        in_progress: { label: 'In Progress', color: 'bg-amber-100 text-amber-700 border-amber-200', icon: Wrench },
        completed: { label: 'Completed', color: 'bg-emerald-100 text-emerald-700 border-emerald-200', icon: CheckCircle2 },
        cancelled: { label: 'Cancelled', color: 'bg-rose-100 text-rose-700 border-rose-200', icon: XCircle }
    };

    const updateForm = useForm({
        status: '',
        notes: '',
        cost: ''
    });

    let selectedLog = null;
    let showUpdateModal = false;

    function openUpdate(log) {
        selectedLog = log;
        $updateForm.status = log.status;
        $updateForm.notes = log.notes || '';
        $updateForm.cost = log.cost || '';
        showUpdateModal = true;
    }

    function submitUpdate() {
        $updateForm.put(`/maintenance/${selectedLog.id}`, {
            onSuccess: () => { showUpdateModal = false; }
        });
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString();
    }
</script>

<AuthenticatedLayout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Maintenance & Repair Tracking</h1>
                <p class="text-slate-500 mt-1">Monitor items undergoing repair and manage maintenance schedules.</p>
            </div>
        </div>

        {#if success}
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-sm font-medium">{success}</span>
            </div>
        {/if}

        <!-- Logs List -->
        <div class="grid grid-cols-1 gap-4">
            {#each logs as log}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            <!-- Item & Reporter Info -->
                            <div class="flex items-start gap-4 flex-1">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0">
                                    <Package size={24} />
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-slate-900">{log.item.name}</h3>
                                        <span class="text-xs text-slate-400 font-medium px-2 py-0.5 bg-slate-100 rounded-md">SKU: {log.item.sku || 'N/A'}</span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-slate-500 font-medium">
                                        <span class="flex items-center gap-1">
                                            <User size={14} class="text-slate-400" />
                                            Reported by {log.reporter?.name || 'Unknown'}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <Calendar size={14} class="text-slate-400" />
                                            {formatDate(log.created_at)}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600 mt-2 line-clamp-2">{log.description}</p>
                                </div>
                            </div>

                            <!-- Progress & Status -->
                            <div class="flex flex-row lg:flex-col lg:items-end justify-between items-center gap-4 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                                <div class="flex flex-row lg:flex-col items-center lg:items-end gap-3 lg:gap-1">
                                    <span class="px-3 py-1 border rounded-full text-[10px] font-bold uppercase tracking-wide flex items-center gap-1.5 {statusMap[log.status].color}">
                                        <svelte:component this={statusMap[log.status].icon} size={12} />
                                        {statusMap[log.status].label}
                                    </span>
                                    {#if log.cost}
                                        <span class="text-sm font-bold text-slate-900">${log.cost}</span>
                                    {/if}
                                </div>
                                
                                <button 
                                    on:click={() => openUpdate(log)}
                                    class="flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-colors shadow-sm active:scale-95"
                                >
                                    Update Status
                                    <ChevronRight size={16} />
                                </button>
                            </div>
                        </div>

                        {#if log.notes}
                            <div class="mt-4 p-3 bg-amber-50/50 rounded-xl border border-amber-100/50 text-xs text-amber-800 italic">
                                <span class="font-bold not-italic">Maintenance Notes:</span> {log.notes}
                            </div>
                        {/if}
                    </div>
                </div>
            {:else}
                <div class="bg-white rounded-2xl border border-slate-200 border-dashed p-20 text-center">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center text-slate-300">
                            <Wrench size={32} />
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">No maintenance records</p>
                            <p class="text-slate-500 text-sm">Damaged items reported will appear here for tracking.</p>
                        </div>
                    </div>
                </div>
            {/each}
        </div>
    </div>

    <!-- Update Status Modal -->
    {#if showUpdateModal}
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-800">Update Maintenance Status</h3>
                    <button type="button" on:click={() => showUpdateModal = false} class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-50 transition-colors">
                        <XCircle size={20} />
                    </button>
                </div>
                <div class="p-6">
                    <form on:submit|preventDefault={submitUpdate} class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">New Status</label>
                            <select bind:value={$updateForm.status} class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white" required>
                                <option value="pending">Pending</option>
                                <option value="in_progress">Start Maintenance</option>
                                <option value="completed">Complete Repair</option>
                                <option value="cancelled">Cancel Maintenance</option>
                            </select>
                        </div>

                        {#if $updateForm.status === 'completed'}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Repair Cost (Optional)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                                    <input type="number" step="0.01" bind:value={$updateForm.cost} class="w-full pl-8 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0.00" />
                                </div>
                            </div>
                        {/if}

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Maintenance Notes</label>
                            <textarea 
                                bind:value={$updateForm.notes} 
                                rows="3" 
                                class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none" 
                                placeholder="Add any technical notes or details about the repair..."
                            ></textarea>
                        </div>

                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" on:click={() => showUpdateModal = false} class="px-5 py-2.5 text-slate-600 font-medium hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                            <button type="submit" disabled={$updateForm.processing} class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all disabled:opacity-50">
                                {$updateForm.processing ? 'Updating...' : 'Save Changes'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    {/if}
</AuthenticatedLayout>
