import { IFilterParams } from '../interfaces/filter-params.interface';
import { QueryParams } from './query-params.model';

export class FilterParams extends QueryParams implements IFilterParams {
    sort_by: string; sort_order: string;
    filter_by: any;
    string: any;
    filter_value: any;
}