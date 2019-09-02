import { Injectable } from '@angular/core';
import { IAppConfig } from './app-config.model';
import { HttpClient } from '@angular/common/http';

//Config controller für die ConfigDatei
@Injectable()
export class AppConfig {
    static settings: IAppConfig;
    constructor(private http: HttpClient) { }
    load() {
        const jsonFile = "assets/config.json";
        return new Promise<void>((resolve, reject) => {
            this.http.get(jsonFile).toPromise().then((response: IAppConfig) => {
                AppConfig.settings = <IAppConfig>response;
                AppConfig.settings.apiServer.route = response.apiServer["route." + response.apiServer.use_route];
                resolve();
            }).catch((response: any) => {
                reject(`Could not load file '${jsonFile}': ${JSON.stringify(response)}`);
            });
        });
    }
}