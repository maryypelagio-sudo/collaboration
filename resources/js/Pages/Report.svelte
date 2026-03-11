<script>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.svelte";
    import { onMount } from "svelte";
    import { FileText, Download, ArrowLeft } from "lucide-svelte";

    onMount(() => {
        // Automatically trigger PDF download when page loads
        window.location.href = "/report/download";
    });
</script>

<AuthenticatedLayout>
    <div class="flex flex-col items-center justify-center min-h-[60vh]">
        <div
            class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm text-center max-w-md"
        >
            <div
                class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
            >
                <FileText size={32} class="text-blue-600" />
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-2">
                Generating Report...
            </h1>
            <p class="text-slate-600 mb-6">
                Your inventory report is being prepared. The download should
                begin automatically.
            </p>

            <div class="flex flex-col gap-3">
                <a
                    href="/report/download"
                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-medium text-sm hover:bg-blue-700 transition-colors shadow-md"
                >
                    <Download size={18} />
                    Download PDF Now
                </a>

                <a
                    href="/"
                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-medium text-sm hover:bg-slate-200 transition-colors"
                >
                    <ArrowLeft size={18} />
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
