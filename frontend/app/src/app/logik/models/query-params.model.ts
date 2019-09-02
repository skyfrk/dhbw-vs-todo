import { IQueryParams } from '../interfaces/query-params.interface';
import { HttpParams } from '@angular/common/http';

export class QueryParams implements IQueryParams {

    constructor() {
        this.limit = 100;
        this.offset = 0;
    }

    limit: number;
    offset: number;

    public static convertToHttpParams(item: any) {
        let params = new HttpParams();

        for (var prop in item) {
            params = params.append(prop, item[prop]);
        }

        return params;
    }
}