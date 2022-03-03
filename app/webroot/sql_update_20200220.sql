ALTER TABLE project_budget_internal_details MODIFY `budget_md` decimal(18,2);
ALTER TABLE project_budget_internal_details MODIFY `budget_erro` decimal(18,2);
ALTER TABLE project_budget_internal_details MODIFY `average` decimal(18,2);

ALTER TABLE project_budget_internals MODIFY `average_daily_rate` decimal(18,2);

ALTER TABLE project_budget_externals MODIFY `budget_erro` decimal(18,2);
ALTER TABLE project_budget_externals MODIFY `ordered_erro` decimal(18,2);
ALTER TABLE project_budget_externals MODIFY `remain_erro` decimal(18,2);
ALTER TABLE project_budget_externals MODIFY `progress_erro` decimal(18,2);
ALTER TABLE project_budget_externals MODIFY `progress_md` decimal(18,2);
ALTER TABLE project_budget_externals MODIFY `man_day` decimal(18,2);

ALTER TABLE project_budget_syns MODIFY `sales_sold` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `sales_to_bill` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `sales_billed` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `sales_paid` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `sales_man_day` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_budget` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_budget_man_day` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_average` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_budget` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_forecast` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_var` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_ordered` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_remain` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_man_day` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_progress` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `external_costs_progress_euro` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `total_costs_budget` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `total_costs_forecast` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `total_costs_var` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `total_costs_engaged` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `total_costs_remain` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `total_costs_man_day` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_forecast` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_var` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_engaged` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_remain` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `internal_costs_forecasted_man_day` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `assign_to_profit_center` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `assign_to_employee` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `roi` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_budget_md` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_last_one_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_last_two_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_last_thr_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_next_one_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_next_two_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `provisional_next_thr_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload_last_one_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload_last_two_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload_last_thr_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload_next_one_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload_next_two_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `workload_next_thr_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `consumed_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `consumed_last_one_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `consumed_last_two_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `consumed_last_thr_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `consumed_next_one_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `consumed_next_two_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `consumed_next_thr_y` decimal(18,2);
ALTER TABLE project_budget_syns MODIFY `overload` decimal(18,2);

ALTER TABLE project_budget_sales MODIFY `sold` decimal(18,2);
ALTER TABLE project_budget_sales MODIFY `man_day` decimal(18,2);

ALTER TABLE project_budget_purchases MODIFY `sold` decimal(18,2);
ALTER TABLE project_budget_purchases MODIFY `man_day` decimal(18,2);

ALTER TABLE project_budget_purchase_invoices MODIFY `billed` decimal(18,2);
ALTER TABLE project_budget_purchase_invoices MODIFY `paid` decimal(18,2);

ALTER TABLE project_budget_provisionals MODIFY `value` decimal(18,2);

ALTER TABLE project_budget_invoices MODIFY `billed` decimal(18,2);
ALTER TABLE project_budget_invoices MODIFY `paid` decimal(18,2);