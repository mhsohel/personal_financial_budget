<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Interactive Simulator State
const activeTab = ref('ledger'); // 'ledger' | 'saas' | 'accounts' | 'loans' | 'analytics'
const simAcmePaid = ref(false);
const simAcmeRenewal = ref('2026-06-29');
const simToast = ref(null);

const simTransactions = ref([
    { id: 1, type: 'income', desc: 'SaaS License Payment - SpaceX', amount: 2500, date: 'Today' },
    { id: 2, type: 'expense', desc: 'AWS Cloud Hosting', amount: 150, date: 'Yesterday' },
    { id: 3, type: 'expense', desc: 'Grocery Store', amount: 42.50, date: '2 days ago' },
]);

const simBudgets = ref([
    { name: 'Food & Dining', spent: 350, limit: 500, color: '#10B981', percentage: 70 },
    { name: 'Entertainment', spent: 80, limit: 100, color: '#F59E0B', percentage: 80 },
    { name: 'SaaS Hosting', spent: 220, limit: 200, color: '#EF4444', percentage: 110 },
]);

const simAccounts = ref([
    { id: 1, name: 'Bank Savings', balance: 1200.00, type: 'bank' },
    { id: 2, name: 'bKash Wallet', balance: 150.00, type: 'mobile_wallet' },
]);
const simTransferDone = ref(false);
const simRepaymentDone = ref(false);

const simLoans = ref([
    { id: 1, person: 'John Doe', type: 'lent', amount: 500, outstanding: 500, due: '2026-07-10', status: 'active' },
    { id: 2, person: 'Jane Smith', type: 'borrowed', amount: 300, outstanding: 300, due: '2026-07-20', status: 'active' },
]);

const simMRR = ref(2850);
const simARR = computed(() => simMRR.value * 12);

const triggerToast = (msg) => {
    simToast.value = msg;
    setTimeout(() => {
        if (simToast.value === msg) {
            simToast.value = null;
        }
    }, 4000);
};

const simulatePay = () => {
    if (simAcmePaid.value) {
        triggerToast("Acme Corp payment has already been logged for this period!");
        return;
    }
    simAcmePaid.value = true;
    simAcmeRenewal.value = '2026-07-29';
    simMRR.value += 350;
    
    // Add transaction to the ledger log
    simTransactions.value.unshift({
        id: Date.now(),
        type: 'income',
        desc: 'SaaS License Payment - Acme Corp',
        amount: 350,
        date: 'Just Now'
    });
    
    triggerToast("💸 Payment logged! +$350 income added and next renewal date advanced.");
};

const simulateTransfer = () => {
    if (simTransferDone.value) {
        triggerToast("Transfer has already been simulated! Reset the demo to try again.");
        return;
    }
    simTransferDone.value = true;
    
    // Update balances
    simAccounts.value[0].balance -= 300;
    simAccounts.value[1].balance += 300;
    
    // Add transfer logs to the ledger list
    simTransactions.value.unshift({
        id: Date.now(),
        type: 'income',
        desc: 'Fund Transfer (To bKash Wallet)',
        amount: 300,
        date: 'Just Now',
        is_transfer: true,
    });
    simTransactions.value.unshift({
        id: Date.now() + 1,
        type: 'expense',
        desc: 'Fund Transfer (From Bank Savings)',
        amount: 300,
        date: 'Just Now',
        is_transfer: true,
    });
    
    triggerToast("💳 Fund Transfer logged! -$300 from Bank Savings, +$300 to bKash Wallet.");
};

const simulateRepayment = () => {
    if (simRepaymentDone.value) {
        triggerToast("Repayment has already been simulated! Reset the demo to try again.");
        return;
    }
    simRepaymentDone.value = true;
    
    // John Doe repayment of $150
    simLoans.value[0].outstanding -= 150;
    simAccounts.value[0].balance += 150; // Bank savings increases
    
    simTransactions.value.unshift({
        id: Date.now(),
        type: 'income',
        desc: 'Loan Repayment - John Doe',
        amount: 150,
        date: 'Just Now'
    });
    
    triggerToast("🤝 Repayment logged! +$150 received from John Doe, outstanding reduced.");
};

const resetSimulator = () => {
    simAcmePaid.value = false;
    simAcmeRenewal.value = '2026-06-29';
    simMRR.value = 2850;
    simAccounts.value = [
        { id: 1, name: 'Bank Savings', balance: 1200.00, type: 'bank' },
        { id: 2, name: 'bKash Wallet', balance: 150.00, type: 'mobile_wallet' },
    ];
    simTransferDone.value = false;
    simRepaymentDone.value = false;
    simLoans.value = [
        { id: 1, person: 'John Doe', type: 'lent', amount: 500, outstanding: 500, due: '2026-07-10', status: 'active' },
        { id: 2, person: 'Jane Smith', type: 'borrowed', amount: 300, outstanding: 300, due: '2026-07-20', status: 'active' },
    ];
    simTransactions.value = [
        { id: 1, type: 'income', desc: 'SaaS License Payment - SpaceX', amount: 2500, date: 'Today' },
        { id: 2, type: 'expense', desc: 'AWS Cloud Hosting', amount: 150, date: 'Yesterday' },
        { id: 3, type: 'expense', desc: 'Grocery Store', amount: 42.50, date: '2 days ago' },
    ];
    triggerToast("🔄 Simulator reset to initial state.");
};

// Interactive FAQs State
const faqs = ref([
    {
        q: "Can I self-host FinFlow for free?",
        a: "Absolutely! The core product is fully open-source and self-hostable. It runs seamlessly on PHP 8.2+ and SQLite, requiring zero complex configuration.",
        open: false
    },
    {
        q: "How does the SaaS License tracking sync with the ledger?",
        a: "Whenever you log a subscription payment on the SaaS License Manager page, it automatically generates a corresponding 'Income' transaction linked to the designated asset account and pushes the client's next renewal date forward by one billing cycle (monthly or yearly).",
        open: false
    },
    {
        q: "Are the financial analytics calculated dynamically?",
        a: "Yes! All metrics, category budget progress bars, receivable/payable balances, and trend projections are calculated on-the-fly directly from your ledger transactions. The system calculates a rolling 3-month savings rate to predict your 3, 6, and 12-month balances.",
        open: false
    },
    {
        q: "Where is my financial data stored?",
        a: "In the self-hosted version, all records are stored directly inside your local SQLite database file, giving you complete data sovereignty. In the Cloud version, your database is encrypted at rest and hosted in a high-security container.",
        open: false
    }
]);

const toggleFaq = (index) => {
    faqs.value[index].open = !faqs.value[index].open;
};
</script>

<template>
    <Head title="FinFlow - SaaS Income & Personal Budget OS" />

    <div class="min-h-screen bg-slate-900 text-slate-100 font-sans selection:bg-indigo-500 selection:text-white overflow-x-hidden relative">
        
        <!-- Background Decorative Gradients -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-violet-650/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-10 left-10 w-80 h-80 bg-emerald-500/5 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <!-- Sticky Header / Navbar -->
        <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand Logo -->
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-650 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-xl font-black bg-gradient-to-r from-white via-slate-100 to-indigo-400 bg-clip-text text-transparent tracking-tight">FinFlow</span>
                </div>

                <!-- Navigation Action Links -->
                <nav v-if="canLogin" class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all duration-150 active:scale-95"
                    >
                        Go to Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold text-slate-600 hover:text-white transition"
                        >
                            Sign In
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition-all duration-150 active:scale-95"
                        >
                            Get Started Free
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 text-center lg:text-left lg:grid lg:grid-cols-12 lg:gap-12 items-center">
            
            <!-- Hero Left: Sales Copy -->
            <div class="lg:col-span-5 space-y-6">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-bold uppercase tracking-wider animate-pulse">
                    ⚡ SQLite Driven Speed & Security
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                    The Smart Multi-Account Budget, Loans & SaaS Revenue OS <br/>
                    <span class="bg-gradient-to-r from-indigo-400 via-violet-400 to-emerald-400 bg-clip-text text-transparent">
                        For Indie Hackers.
                    </span>
                </h1>
                
                <p class="text-base sm:text-lg text-slate-600 font-medium leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Consolidate cash flows across bank accounts and mobile wallets. Track daily budgets, log lent/borrowed debts, execute transfers, and manage recurring client subscription revenues.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                    <Link
                        v-if="canRegister && !$page.props.auth.user"
                        :href="route('register')"
                        class="px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/20 transition-all duration-150 active:scale-95"
                    >
                        Create Your Account
                    </Link>
                    <Link
                        v-else-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/20 transition-all duration-150 active:scale-95"
                    >
                        Open Dashboard
                    </Link>
                    <a
                        href="#interactive-demo"
                        class="px-8 py-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-sm transition"
                    >
                        Try Live Demo
                    </a>
                </div>

                <div class="flex items-center justify-center lg:justify-start gap-6 pt-6 text-slate-700 text-xs font-bold">
                    <div class="flex items-center gap-1.5">
                        <svg class="h-4.5 w-4.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        No Credit Card Required
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="h-4.5 w-4.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        Self-Hostable Core
                    </div>
                </div>
            </div>

            <!-- Hero Right: Interactive Dashboard Widget -->
            <div class="lg:col-span-7 mt-12 lg:mt-0" id="interactive-demo">
                
                <!-- Mock Browser Panel Container -->
                <div class="relative bg-slate-800/80 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-sm">
                    
                    <!-- Browser Chrome Header -->
                    <div class="bg-slate-900/90 px-4 py-3 border-b border-slate-700/80 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                            <span class="ml-4 text-xs font-bold text-slate-700 tracking-wider">FINFLOW DEMO PLATFORM</span>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                @click="resetSimulator" 
                                class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-[10px] font-bold text-indigo-400 rounded border border-slate-750 transition flex items-center gap-1"
                                title="Reset Simulator"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18" />
                                </svg>
                                Reset Demo
                            </button>
                        </div>
                    </div>

                    <!-- Simulator Tabs -->
                    <div class="flex overflow-x-auto border-b border-slate-700/50 bg-slate-900/30 text-xs font-bold text-slate-600 whitespace-nowrap scrollbar-none">
                        <button 
                            @click="activeTab = 'ledger'"
                            :class="activeTab === 'ledger' ? 'text-indigo-405 border-b-2 border-indigo-500 bg-slate-800/60' : 'hover:text-white'"
                            class="px-5 py-3.5 transition"
                        >
                            📊 Personal Ledger & Budgets
                        </button>
                        
                        <button 
                            @click="activeTab = 'accounts'"
                            :class="activeTab === 'accounts' ? 'text-indigo-405 border-b-2 border-indigo-500 bg-slate-800/60' : 'hover:text-white'"
                            class="px-5 py-3.5 transition"
                        >
                            💳 Multi-Account & Transfers
                        </button>
                        <button 
                            @click="activeTab = 'loans'"
                            :class="activeTab === 'loans' ? 'text-indigo-405 border-b-2 border-indigo-500 bg-slate-800/60' : 'hover:text-white'"
                            class="px-5 py-3.5 transition"
                        >
                            🤝 Loans & Debts
                        </button>
                        <button 
                            @click="activeTab = 'analytics'"
                            :class="activeTab === 'analytics' ? 'text-indigo-405 border-b-2 border-indigo-500 bg-slate-800/60' : 'hover:text-white'"
                            class="px-5 py-3.5 transition"
                        >
                            📈 Predictive Forecasts
                        </button>
                        <button 
                            @click="activeTab = 'saas'"
                            :class="activeTab === 'saas' ? 'text-indigo-405 border-b-2 border-indigo-500 bg-slate-800/60' : 'hover:text-white'"
                            class="px-5 py-3.5 transition"
                        >
                            💼 SaaS Subscriptions
                        </button>
                    </div>

                    <!-- Simulator Body -->
                    <div class="p-6 min-h-[360px] flex flex-col justify-between">
                        
                        <!-- TAB 1: Ledger & Budgets -->
                        <div v-if="activeTab === 'ledger'" class="space-y-6">
                            <!-- Simulated Budget Limit Bars -->
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Simulated Budgets (Limits)</h4>
                                <div v-for="bg in simBudgets" :key="bg.name" class="space-y-1">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-slate-300">{{ bg.name }}</span>
                                        <span class="text-slate-600">${{ bg.spent }} / ${{ bg.limit }} ({{ bg.percentage }}%)</span>
                                    </div>
                                    <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border border-slate-700">
                                        <div 
                                            class="h-full rounded-full transition-all duration-500" 
                                            :style="{ width: Math.min(bg.percentage, 100) + '%', backgroundColor: bg.color }"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ledger Transactions List -->
                            <div class="space-y-2.5">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Simulated Recent Transactions</h4>
                                <div class="space-y-2 max-h-[140px] overflow-y-auto">
                                    <div 
                                        v-for="tx in simTransactions" 
                                        :key="tx.id"
                                        class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/40 border border-slate-700 text-xs transition duration-300 hover:bg-slate-900"
                                    >
                                        <div class="flex items-center gap-2.5">
                                            <span 
                                                class="w-2.5 h-2.5 rounded-full"
                                                :class="tx.type === 'income' ? 'bg-emerald-500' : 'bg-rose-500'"
                                            ></span>
                                            <span class="font-bold text-slate-200">{{ tx.desc }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-slate-700">{{ tx.date }}</span>
                                            <span 
                                                class="font-black"
                                                :class="tx.type === 'income' ? 'text-emerald-400' : 'text-rose-500'"
                                            >
                                                {{ tx.type === 'income' ? '+' : '-' }}${{ tx.amount }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: SaaS Manager -->
                        <div v-if="activeTab === 'saas'" class="space-y-6">
                            <!-- SaaS KPI metrics -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-700">Active SaaS Clients</span>
                                    <p class="text-xl font-extrabold text-indigo-400 mt-1">2</p>
                                </div>
                                <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-700">Monthly Revenue (MRR)</span>
                                    <p class="text-xl font-extrabold text-emerald-400 mt-1">${{ simMRR.toLocaleString() }}</p>
                                </div>
                                <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-700">Annualized Run Rate (ARR)</span>
                                    <p class="text-xl font-extrabold text-emerald-400 mt-1">${{ simARR.toLocaleString() }}</p>
                                </div>
                            </div>

                            <!-- Interactive Client Payment Log Demonstration -->
                            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-700 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h5 class="text-sm font-extrabold text-white">Interactive Demonstration</h5>
                                        <p class="text-[11px] text-slate-600 mt-0.5">Log an upcoming payment for SaaS client "Acme Corp" ($350/mo)</p>
                                    </div>
                                    <button 
                                        @click="simulatePay"
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[11px] rounded-lg shadow-md transition"
                                        :class="simAcmePaid ? 'opacity-50 cursor-not-allowed bg-slate-700' : ''"
                                    >
                                        Log Payment
                                    </button>
                                </div>

                                <div class="divide-y divide-slate-700 text-xs">
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="font-bold text-slate-300">SpaceX (RocketControl)</span>
                                        <div class="flex items-center gap-3.5">
                                            <span class="text-slate-600 font-bold">$2,500.00 / month</span>
                                            <span class="px-2 py-0.5 rounded bg-emerald-950/40 text-emerald-400 border border-emerald-900/50 text-[10px] font-bold">Active</span>
                                        </div>
                                    </div>
                                    <div class="py-2.5 flex items-center justify-between">
                                        <div>
                                            <span class="font-bold text-slate-300">Acme Corp (ApiGateway)</span>
                                            <p class="text-[10px] text-slate-600 mt-0.5">Renewal: {{ simAcmeRenewal }}</p>
                                        </div>
                                        <div class="flex items-center gap-3.5">
                                            <span class="text-slate-600 font-bold">$350.00 / month</span>
                                            <span 
                                                class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                :class="simAcmePaid ? 'bg-emerald-950/40 text-emerald-400 border border-emerald-900/50' : 'bg-amber-950/40 text-amber-400 border border-amber-900/50'"
                                            >
                                                {{ simAcmePaid ? 'Paid' : 'Pending' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: Multi-Account & Transfers -->
                        <div v-if="activeTab === 'accounts'" class="space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Simulated Accounts & Balances</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="acc in simAccounts" :key="acc.id" class="p-4 rounded-xl border border-slate-700 bg-slate-900/60 relative overflow-hidden">
                                    <!-- Decorative glow -->
                                    <div class="absolute -right-6 -bottom-6 w-20 h-20 rounded-full blur-xl opacity-20 bg-indigo-500"></div>
                                    
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-600">
                                        <span>{{ acc.name }}</span>
                                        <span class="text-[9px] uppercase tracking-wider px-2 py-0.5 rounded bg-slate-800 border border-slate-700">{{ acc.type === 'mobile_wallet' ? 'mobile wallet' : acc.type }}</span>
                                    </div>
                                    <p class="text-2xl font-black text-white mt-2.5">
                                        ${{ acc.balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                    </p>
                                </div>
                            </div>

                            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-700 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h5 class="text-xs font-extrabold text-white">Transfer Simulator</h5>
                                        <p class="text-[10px] text-slate-600 mt-0.5">Move $300.00 from Bank Savings to bKash Wallet</p>
                                    </div>
                                    <button 
                                        @click="simulateTransfer"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-[10px] rounded-lg shadow-md transition"
                                        :class="simTransferDone ? 'opacity-50 cursor-not-allowed bg-slate-700' : ''"
                                    >
                                        Log Transfer
                                    </button>
                                </div>

                                <div v-if="simTransferDone" class="text-xs space-y-2 border-t border-slate-700/50 pt-2.5">
                                    <div class="flex items-center justify-between text-[10px] text-slate-450">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Bank Savings (Expense side)</span>
                                        </div>
                                        <span class="font-bold text-rose-450">-$300.00</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-455">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>bKash Wallet (Income side)</span>
                                        </div>
                                        <span class="font-bold text-emerald-450">+$300.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: Loans & Debts -->
                        <div v-if="activeTab === 'loans'" class="space-y-4">
                            <!-- Loan statistics -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-700">Total Receivable</span>
                                    <p class="text-lg font-extrabold text-emerald-400 mt-1">
                                        ${{ simLoans.reduce((sum, item) => item.type === 'lent' ? sum + item.outstanding : sum, 0) }}
                                    </p>
                                </div>
                                <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-700">Total Payable</span>
                                    <p class="text-lg font-extrabold text-rose-550 mt-1">
                                        ${{ simLoans.reduce((sum, item) => item.type === 'borrowed' ? sum + item.outstanding : sum, 0) }}
                                    </p>
                                </div>
                                <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-700">Overdue Status</span>
                                    <p class="text-lg font-extrabold text-amber-500 mt-1">1 Overdue</p>
                                </div>
                            </div>

                            <!-- Interactive Repayment log -->
                            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-700 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h5 class="text-xs font-extrabold text-white">Interactive Repayment Demo</h5>
                                        <p class="text-[10px] text-slate-600 mt-0.5">Log a $150.00 repayment from debtor "John Doe"</p>
                                    </div>
                                    <button 
                                        @click="simulateRepayment"
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] rounded-lg shadow-md transition"
                                        :class="simRepaymentDone ? 'opacity-50 cursor-not-allowed bg-slate-700' : ''"
                                    >
                                        Log Repayment
                                    </button>
                                </div>

                                <div class="divide-y divide-slate-700 text-xs">
                                    <div v-for="loan in simLoans" :key="loan.id" class="py-2.5 flex items-center justify-between">
                                        <div>
                                            <span class="font-bold text-slate-300">{{ loan.person }}</span>
                                            <span class="text-[9px] uppercase font-semibold px-2 py-0.5 ml-2.5 rounded bg-slate-800 text-slate-600 border border-slate-700">
                                                {{ loan.type === 'lent' ? 'Lent' : 'Borrowed' }}
                                            </span>
                                            <p class="text-[10px] text-slate-600 mt-0.5">Due: {{ loan.due }}</p>
                                        </div>
                                        <div class="flex items-center gap-3.5">
                                            <span class="text-slate-600 font-bold">Outstanding: ${{ loan.outstanding }}</span>
                                            <span 
                                                class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                :class="loan.outstanding < loan.amount ? 'bg-emerald-950/40 text-emerald-400 border border-emerald-900/50' : (loan.id === 1 ? 'bg-rose-950/40 text-rose-400 border border-rose-900/50 animate-pulse' : 'bg-slate-800 text-slate-600')"
                                            >
                                                {{ loan.outstanding === 0 ? 'Settled' : (loan.outstanding < loan.amount ? 'Partial' : (loan.id === 1 ? 'Overdue' : 'Active')) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 5: Predictive Analytics -->
                        <div v-if="activeTab === 'analytics'" class="space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Simulated 12-Month Projections (Savings rate: 85%)</h4>
                            
                            <!-- Custom SVG Line Graph Mockup -->
                            <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700">
                                <svg class="w-full h-32" viewBox="0 0 400 100" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="chart-grad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#4F46E5" stop-opacity="0.3"></stop>
                                            <stop offset="100%" stop-color="#4F46E5" stop-opacity="0.0"></stop>
                                        </linearGradient>
                                    </defs>
                                    
                                    <!-- Grid lines -->
                                    <line x1="0" y1="20" x2="400" y2="20" stroke="#334155" stroke-dasharray="4" stroke-width="0.5" />
                                    <line x1="0" y1="50" x2="400" y2="50" stroke="#334155" stroke-dasharray="4" stroke-width="0.5" />
                                    <line x1="0" y1="80" x2="400" y2="80" stroke="#334155" stroke-dasharray="4" stroke-width="0.5" />
                                    
                                    <!-- Shaded Area -->
                                    <path d="M 0 90 Q 100 65, 200 45 T 400 15 L 400 100 L 0 100 Z" fill="url(#chart-grad)" />
                                    
                                    <!-- Graph Line -->
                                    <path d="M 0 90 Q 100 65, 200 45 T 400 15" fill="none" stroke="#6366F1" stroke-width="3" />
                                    
                                    <!-- Circle nodes -->
                                    <circle cx="200" cy="45" r="4.5" fill="#818CF8" />
                                    <circle cx="400" cy="15" r="4.5" fill="#34D399" />
                                </svg>
                                
                                <div class="flex items-center justify-between text-[10px] font-bold text-slate-700 mt-2">
                                    <span>Today (Base Balance)</span>
                                    <span>6 Months Forecast (+${{ (simMRR * 6 - 350).toLocaleString() }})</span>
                                    <span>12 Months Target (+${{ (simMRR * 12 - 700).toLocaleString() }})</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-800/30 p-3 rounded-lg border border-slate-700">
                                    <span class="text-[9px] font-bold text-slate-700 uppercase">3-Month Savings Forecast</span>
                                    <p class="text-sm font-black text-white mt-0.5">+${{ (simMRR * 3 - 150).toLocaleString() }}</p>
                                </div>
                                <div class="bg-slate-800/30 p-3 rounded-lg border border-slate-700">
                                    <span class="text-[9px] font-bold text-slate-700 uppercase">12-Month Accumulation</span>
                                    <p class="text-sm font-black text-emerald-400 mt-0.5">+${{ (simMRR * 12 - 600).toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Toast Notification inside Simulator -->
                        <div class="h-6 mt-2">
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="transform translate-y-2 opacity-0"
                                enter-to-class="transform translate-y-0 opacity-100"
                                leave-active-class="transition duration-150 ease-in"
                                leave-from-class="transform translate-y-0 opacity-100"
                                leave-to-class="transform translate-y-2 opacity-0"
                            >
                                <div v-if="simToast" class="text-xs font-bold text-indigo-300 text-center flex items-center justify-center gap-1.5 bg-slate-900/80 border border-slate-700 rounded-lg py-1">
                                    {{ simToast }}
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Columns Section -->
        <section class="border-t border-slate-800 bg-slate-950/30 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-4 max-w-2xl mx-auto">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">
                        Built for builders, creators, and freelancers.
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed font-semibold">
                        A robust, zero-bloat personal ledger backed by Laravel 11. Built specifically to tackle both personal multi-account tracking and client subscription revenues simultaneously.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                    <!-- Feature 1 -->
                    <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl space-y-4 hover:border-slate-700 transition duration-155 hover:-translate-y-1 relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition duration-300"></div>
                        <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Multi-Account & Transfers</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Set up separate accounts for Bank Savings, Cash, or mobile wallets (bKash). Execute instant, double-entry transfers that keep ledger balances synchronized.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl space-y-4 hover:border-slate-700 transition duration-155 hover:-translate-y-1 relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-rose-500/5 rounded-full blur-xl group-hover:bg-rose-500/10 transition duration-300"></div>
                        <div class="w-10 h-10 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Visual Budget Warnings</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Set monthly limits on expense categories. Progress bars dynamically shift color (green &rarr; orange warning &rarr; red alert) to prevent overspending.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl space-y-4 hover:border-slate-700 transition duration-155 hover:-translate-y-1 relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition duration-300"></div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">SaaS Client Log & MRR</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Manage licenses and SaaS client subscriptions. Log payments with one click to auto-increment renewal dates and update the central ledger.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl space-y-4 hover:border-slate-700 transition duration-155 hover:-translate-y-1 relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-amber-500/5 rounded-full blur-xl group-hover:bg-amber-500/10 transition duration-300"></div>
                        <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Loans & Debts Tracker</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Log lent or borrowed funds. Visual warning badges notify you of upcoming and overdue deadlines. Automatically record repayments back into account statements.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl space-y-4 hover:border-slate-700 transition duration-155 hover:-translate-y-1 relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-violet-500/5 rounded-full blur-xl group-hover:bg-violet-500/10 transition duration-300"></div>
                        <div class="w-10 h-10 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Smart Predictions</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Project your future finances dynamically. Generates 3, 6, and 12-month savings forecasts based on your rolling 3-month averages.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Screenshots/Visual Showcase Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center space-y-12">
            <div class="space-y-4 max-w-2xl mx-auto">
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Take a Look Inside the Platform</h2>
                <p class="text-xs text-slate-600 font-semibold leading-relaxed">
                    Designed from the ground up for maximum visual clarity, readability, and interface delight.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 max-w-7xl mx-auto">
                <!-- Outflow Stack Card -->
                <div class="bg-slate-900/60 p-6 border border-slate-800 rounded-2xl flex flex-col justify-between text-left space-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Reports Index</span>
                        <h3 class="text-lg font-extrabold text-white mt-1">Monthly Analytics Breakdown</h3>
                        <p class="text-xs text-slate-600 mt-1">Get precise details of where your money is flowing using elegant custom SVG charts.</p>
                    </div>
                    <div class="bg-slate-950/40 p-4 rounded-xl border border-slate-700/70 mt-4 h-44 flex items-end justify-between gap-2">
                        <!-- Bar chart elements -->
                        <div class="w-full space-y-2 text-center">
                            <div class="w-full bg-slate-800 rounded h-24 relative overflow-hidden">
                                <div class="bg-indigo-500 absolute bottom-0 left-0 right-0 h-[40%]"></div>
                            </div>
                            <span class="text-[9px] text-slate-550 font-bold">Jan</span>
                        </div>
                        <div class="w-full space-y-2 text-center">
                            <div class="w-full bg-slate-800 rounded h-24 relative overflow-hidden">
                                <div class="bg-indigo-500 absolute bottom-0 left-0 right-0 h-[55%]"></div>
                            </div>
                            <span class="text-[9px] text-slate-550 font-bold">Feb</span>
                        </div>
                        <div class="w-full space-y-2 text-center">
                            <div class="w-full bg-slate-800 rounded h-24 relative overflow-hidden">
                                <div class="bg-indigo-500 absolute bottom-0 left-0 right-0 h-[80%]"></div>
                            </div>
                            <span class="text-[9px] text-slate-550 font-bold">Mar</span>
                        </div>
                        <div class="w-full space-y-2 text-center">
                            <div class="w-full bg-slate-800 rounded h-24 relative overflow-hidden">
                                <div class="bg-emerald-500 absolute bottom-0 left-0 right-0 h-[95%]"></div>
                            </div>
                            <span class="text-[9px] text-slate-550 font-bold">Apr</span>
                        </div>
                    </div>
                </div>

                <!-- Client Ledger Card -->
                <div class="bg-slate-900/60 p-6 border border-slate-800 rounded-2xl flex flex-col justify-between text-left space-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">SaaS Subscriptions</span>
                        <h3 class="text-lg font-extrabold text-white mt-1">Client Payment Audit Trails</h3>
                        <p class="text-xs text-slate-600 mt-1">Keep track of client payments with a dedicated payment history log in your manager dashboard.</p>
                    </div>
                    
                    <div class="bg-slate-955/40 p-4 rounded-xl border border-slate-700/70 mt-4 space-y-2.5 h-44 overflow-hidden text-[11px] font-medium text-slate-600">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 text-slate-700 font-bold uppercase text-[9px]">
                            <span>Client</span>
                            <span>Billing</span>
                            <span>Status</span>
                        </div>
                        <div class="flex justify-between items-center py-0.5">
                            <span class="font-bold text-slate-300">SpaceX</span>
                            <span>$2,500.00/mo</span>
                            <span class="text-emerald-400 font-bold">Paid</span>
                        </div>
                        <div class="flex justify-between items-center py-0.5">
                            <span class="font-bold text-slate-300">Acme Corp</span>
                            <span>$350.00/mo</span>
                            <span class="text-amber-400 font-bold">Pending</span>
                        </div>
                        <div class="flex justify-between items-center py-0.5">
                            <span class="font-bold text-slate-300">Stark Industries</span>
                            <span>$1,200.00/yr</span>
                            <span class="text-emerald-400 font-bold">Paid</span>
                        </div>
                    </div>
                </div>

                <!-- Accounts Flow Card -->
                <div class="bg-slate-900/60 p-6 border border-slate-800 rounded-2xl flex flex-col justify-between text-left space-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Unified cash flow</span>
                        <h3 class="text-lg font-extrabold text-white mt-1">Account Statements</h3>
                        <p class="text-xs text-slate-600 mt-1">Get an institutional-grade breakdown of cash inflows, outflows, and net flows for all wallets.</p>
                    </div>
                    
                    <div class="bg-slate-950/40 p-3 rounded-xl border border-slate-700/70 mt-4 overflow-hidden text-[10px] font-medium text-slate-600 flex flex-col justify-between h-44">
                        <div class="grid grid-cols-4 border-b border-slate-800/80 pb-1.5 text-slate-700 font-bold uppercase text-[8px] gap-1">
                            <span>Account</span>
                            <span class="text-right">Inflow</span>
                            <span class="text-right">Outflow</span>
                            <span class="text-right">Ending</span>
                        </div>
                        <div class="grid grid-cols-4 py-1.5 border-b border-slate-800/20 gap-1">
                            <span class="font-bold text-slate-300 truncate">Bank</span>
                            <span class="text-right text-emerald-450">+$500</span>
                            <span class="text-right text-rose-455">-$200</span>
                            <span class="text-right font-bold text-white">$1,300</span>
                        </div>
                        <div class="grid grid-cols-4 py-1.5 border-b border-slate-800/20 gap-1">
                            <span class="font-bold text-slate-300 truncate">bKash</span>
                            <span class="text-right text-emerald-450">+$300</span>
                            <span class="text-right text-rose-455">-$50</span>
                            <span class="text-right font-bold text-white">$400</span>
                        </div>
                        <div class="grid grid-cols-4 py-1.5 gap-1 font-bold text-slate-300 bg-slate-900/40 px-1 rounded mt-0.5">
                            <span class="truncate">Total</span>
                            <span class="text-right text-emerald-450">+$800</span>
                            <span class="text-right text-rose-455">-$250</span>
                            <span class="text-right text-indigo-400">$1,700</span>
                        </div>
                    </div>
                </div>

                <!-- Loans & Debts Showcase Card -->
                <div class="bg-slate-900/60 p-6 border border-slate-800 rounded-2xl flex flex-col justify-between text-left space-y-4">
                    <div>
                        <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest">Lending & Debt Audit</span>
                        <h3 class="text-lg font-extrabold text-white mt-1">Outstanding Balances</h3>
                        <p class="text-xs text-slate-600 mt-1">Easily trace who owes you and who you owe, with precise repayment breakdowns and status indicators.</p>
                    </div>
                    
                    <div class="bg-slate-950/40 p-3.5 rounded-xl border border-slate-700/70 mt-4 space-y-3 h-44 overflow-hidden text-[10px] font-medium text-slate-600">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 text-slate-700 font-bold uppercase text-[8px]">
                            <span>Person / Type</span>
                            <span>Repayment Progress</span>
                            <span>Status</span>
                        </div>
                        <div class="space-y-1 py-0.5">
                            <div class="flex justify-between items-center text-[9px]">
                                <span class="font-bold text-slate-300">John Doe (Lent)</span>
                                <span>$350 outstanding of $500</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-500 h-full" style="width: 30%;"></div>
                            </div>
                        </div>
                        <div class="space-y-1 py-0.5">
                            <div class="flex justify-between items-center text-[9px]">
                                <span class="font-bold text-slate-300">Jane Smith (Borrowed)</span>
                                <span>$300 outstanding of $300</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-500 h-full" style="width: 0%;"></div>
                            </div>
                        </div>
                        <div class="space-y-1 py-0.5">
                            <div class="flex justify-between items-center text-[9px]">
                                <span class="font-bold text-slate-300">Stark Labs (Lent)</span>
                                <span>$0 outstanding of $1,000</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-full" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Tier Grid Section (SaaS commercial appeal) -->
        <section class="border-t border-slate-800 bg-slate-900/30 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-4 max-w-2xl mx-auto">
                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Pricing Tiers</span>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Simple, flexible pricing.</h2>
                    <p class="text-sm text-slate-600 leading-relaxed font-semibold">
                        Deploy open-source on your own hardware or choose our managed cloud for automated features.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- Self-Hosted Plan -->
                    <div class="p-8 bg-slate-900/60 border border-slate-800 rounded-3xl flex flex-col justify-between space-y-6 hover:border-slate-700 transition duration-150 relative overflow-hidden group">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white">Self-Hosted Core</h3>
                                <span class="px-2.5 py-1 rounded bg-slate-800 border border-slate-700 text-xs font-bold text-slate-600">Open Source</span>
                            </div>
                            <p class="text-xs text-slate-450 leading-relaxed">
                                Complete privacy and control. Spin up your own personal instance on any VPS or local machine.
                            </p>
                            <div class="flex items-baseline gap-1 text-white">
                                <span class="text-4xl font-black">$0</span>
                                <span class="text-xs font-bold text-slate-700">/ forever</span>
                            </div>
                            
                            <hr class="border-slate-800" />
                            
                            <ul class="space-y-3.5 text-xs text-slate-350">
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Unlimited Accounts & Wallets
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Visual Budget Limit Warnings
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    SaaS License & MRR Manager
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Personal Loans & Debts tracker
                                </li>
                            </ul>
                        </div>
                        <a 
                            href="https://github.com" 
                            target="_blank"
                            class="w-full py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-100 font-extrabold text-xs text-center border border-slate-700 transition"
                        >
                            View Docs & Clone Repo
                        </a>
                    </div>

                    <!-- Cloud Plan -->
                    <div class="p-8 bg-slate-900/80 border-2 border-indigo-500 rounded-3xl flex flex-col justify-between space-y-6 relative overflow-hidden group shadow-xl shadow-indigo-650/10">
                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white">FinFlow Cloud</h3>
                                <span class="px-2.5 py-1 rounded bg-indigo-500/20 border border-indigo-500/30 text-xs font-bold text-indigo-400">Popular</span>
                            </div>
                            <p class="text-xs text-slate-450 leading-relaxed">
                                No setup required. Sync with external bank APIs automatically, export statements, and log in on multiple devices.
                            </p>
                            <div class="flex items-baseline gap-1 text-white">
                                <span class="text-4xl font-black">$9</span>
                                <span class="text-xs font-bold text-slate-700">/ month</span>
                            </div>
                            
                            <hr class="border-slate-800" />
                            
                            <ul class="space-y-3.5 text-xs text-slate-350">
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <strong>Everything in Self-Hosted</strong>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Automated Bank Sync (Plaid API)
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Multi-Device Synchronization
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Priority Chat & Email Support
                                </li>
                            </ul>
                        </div>
                        
                        <Link 
                            v-if="canRegister && !$page.props.auth.user"
                            :href="route('register')"
                            class="w-full py-3 rounded-xl bg-indigo-650 hover:bg-indigo-600 text-white font-extrabold text-xs text-center shadow-lg shadow-indigo-650/30 transition-all duration-150 active:scale-95"
                        >
                            Try 14-Days Cloud Trial
                        </Link>
                        <Link 
                            v-else
                            :href="route('dashboard')"
                            class="w-full py-3 rounded-xl bg-indigo-650 hover:bg-indigo-600 text-white font-extrabold text-xs text-center shadow-lg shadow-indigo-650/30 transition-all duration-150 active:scale-95"
                        >
                            Go to Dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 space-y-12">
            <div class="text-center space-y-4 max-w-2xl mx-auto">
                <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">TESTIMONIALS</span>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Approved by indie builders.</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <!-- Testimonial 1 -->
                <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl flex flex-col justify-between space-y-6">
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        "The SQLite-backed local performance is insane. I self-host it on a tiny $5 VPS and page loads are completely instantaneous. Being able to audit both my micro-SaaS MRR and daily personal expenses in one unified database is amazing."
                    </p>
                    <div class="flex items-center gap-3.5 border-t border-slate-800/80 pt-4">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-emerald-500 flex items-center justify-center font-bold text-[10px] text-white">AK</div>
                        <div>
                            <h4 class="text-xs font-bold text-white">Arvid K.</h4>
                            <span class="text-[9px] font-bold text-slate-700">Founder, ShipQuick</span>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl flex flex-col justify-between space-y-6">
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        "I used to bounce between spreadsheets and complex budgeting apps. FinFlow gives me visual alarms when my food budget is nearing the limit, and lets me track cash balances across mobile wallets (bKash) easily."
                    </p>
                    <div class="flex items-center gap-3.5 border-t border-slate-800/80 pt-4">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-violet-500 to-rose-500 flex items-center justify-center font-bold text-[10px] text-white">SM</div>
                        <div>
                            <h4 class="text-xs font-bold text-white">Sarah M.</h4>
                            <span class="text-[9px] font-bold text-slate-700">Freelance UX Designer</span>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="p-6 bg-slate-900/50 border border-slate-800 rounded-2xl flex flex-col justify-between space-y-6">
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        "The Loans & Debts module is exactly what was missing. Now when I lend money to colleagues, I register it on the portal, assign a due date, and log repayments with two clicks. It even updates my accounts ledger automatically."
                    </p>
                    <div class="flex items-center gap-3.5 border-t border-slate-800/80 pt-4">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-indigo-500 flex items-center justify-center font-bold text-[10px] text-white">JN</div>
                        <div>
                            <h4 class="text-xs font-bold text-white">Julian N.</h4>
                            <span class="text-[9px] font-bold text-slate-700">Co-Founder, SaaSify</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive FAQ Accordion Section -->
        <section class="border-t border-slate-800 bg-slate-950/20 py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-4 max-w-2xl mx-auto">
                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">QUESTIONS & ANSWERS</span>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Frequently Asked Questions</h2>
                </div>

                <div class="space-y-4 max-w-3xl mx-auto">
                    <div 
                        v-for="(faq, idx) in faqs" 
                        :key="idx" 
                        class="bg-slate-900/50 border border-slate-800 rounded-2xl overflow-hidden transition"
                    >
                        <button 
                            @click="toggleFaq(idx)"
                            class="w-full p-6 text-left flex justify-between items-center gap-4 hover:bg-slate-800/20 transition"
                        >
                            <span class="text-sm font-bold text-white">{{ faq.q }}</span>
                            <span class="text-indigo-400 font-extrabold text-lg">
                                {{ faq.open ? '−' : '+' }}
                            </span>
                        </button>
                        <div 
                            v-if="faq.open" 
                            class="px-6 pb-6 text-xs text-slate-600 leading-relaxed font-medium border-t border-slate-800/30 pt-4"
                        >
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Call to action -->
        <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <div class="bg-gradient-to-tr from-slate-900 via-slate-800 to-indigo-950 p-8 sm:p-12 rounded-3xl border border-slate-800 space-y-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Ready to get your finance flow under control?</h2>
                <p class="text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
                    Set up your categories, allocate budgets, register client products, and start auditing subscription fees in minutes.
                </p>
                <div class="pt-4 flex flex-col sm:flex-row justify-center gap-3">
                    <Link
                        v-if="canRegister && !$page.props.auth.user"
                        :href="route('register')"
                        class="px-8 py-3.5 bg-white hover:bg-slate-100 text-slate-900 font-extrabold text-xs rounded-xl shadow-lg transition"
                    >
                        Sign Up Now
                    </Link>
                    <Link
                        v-else-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-8 py-3.5 bg-white hover:bg-slate-100 text-slate-900 font-extrabold text-xs rounded-xl shadow-lg transition"
                    >
                        Go to Dashboard
                    </Link>
                    <Link
                        :href="route('login')"
                        class="px-8 py-3.5 bg-slate-805 hover:bg-slate-700 text-white font-extrabold text-xs rounded-xl border border-slate-750 transition"
                    >
                        Sign In
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 bg-slate-950/20 py-10 text-center text-xs text-slate-700 font-medium">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
                <div class="flex items-center justify-center gap-2">
                    <span class="font-bold text-slate-600">FinFlow</span>
                    <span>&copy; 2026. All rights reserved.</span>
                </div>
                <div class="flex items-center justify-center gap-4 text-[10px] text-slate-600">
                    <span>Powered by Laravel v{{ laravelVersion }}</span>
                    <span>PHP v{{ phpVersion }}</span>
                    <span>SQLite Storage</span>
                </div>
                <div class="flex items-center justify-center gap-4 text-[10px] text-slate-600 border-t border-slate-800/50 pt-4 max-w-sm mx-auto">
                    <span>Developed by: PRANTIK-SOFT</span>
                    <span>Mobile: +8801735254295</span>
                    <span>Email: contact@prantiksoft.com</span>
                </div>
            </div>
        </footer>
    </div>
</template>
