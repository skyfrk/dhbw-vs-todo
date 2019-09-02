//Interface für AppConfig.json
export interface IAppConfig {
    apiServer: {
        route: string,
        use_route: string;
    };
    app:{
        assets:string;
    }
}