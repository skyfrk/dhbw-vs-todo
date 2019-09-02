import { IQueryParams } from './query-params.interface';

export interface IFilterParams extends IQueryParams {
    sort_by: string;
    sort_order: string;
    filter_by; string;
    filter_value;
}