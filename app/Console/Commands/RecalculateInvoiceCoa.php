<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Data\InvoicePembayaranContractItem;
use App\Models\Master\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class RecalculateInvoiceCoa extends Command
{
    protected $signature   = 'invoice:recalculate-coa';
    protected $description = 'Recalculate COA current_balance from Invoice Contract items (run once after deploy)';

    public function handle()
    {
        $this->info('Calculating invoice totals per COA...');

        $totals = InvoicePembayaranContractItem::query()
            ->whereNotNull('id_md_chart_of_account')
            ->where('nominal_invoice', '>', 0)
            ->selectRaw('id_md_chart_of_account, SUM(nominal_invoice) as total')
            ->groupBy('id_md_chart_of_account')
            ->get();

        if ($totals->isEmpty()) {
            $this->info('No invoice items with COA found. Nothing to do.');
            return 0;
        }

        DB::beginTransaction();
        try {
            foreach ($totals as $t) {
                $coa = ChartOfAccount::find($t->id_md_chart_of_account);
                if (!$coa) {
                    $this->warn("  COA ID {$t->id_md_chart_of_account} not found, skipped.");
                    continue;
                }
                $coa->increment('current_balance', (float) $t->total);
                $this->info("  COA {$coa->no_account} - {$coa->account_name}: +" . number_format($t->total, 2));
            }
            DB::commit();
            $this->info('Done! COA current_balance updated for ' . $totals->count() . ' account(s).');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
