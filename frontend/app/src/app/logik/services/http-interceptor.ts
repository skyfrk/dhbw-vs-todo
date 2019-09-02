import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { Credentials } from '../interfaces/credentials.interface';
@Injectable()
export class TokenInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    var token:string = "";

    //JWT aus localstorage laden
    token = localStorage.getItem("jwt");
    if(token == null || token == ""){
      //wenn nicht in localstorage -> aus Sessionstorage laden
      token = sessionStorage.getItem("jwt");
    }
    //wenn gar kein JWT vorhande -> leeren token schicken
    if(token == null || token == ""){
        request = request.clone({
            setHeaders: {
              Authorization: `Bearer ${""}`
            }
          });
    }
    //wenn gar kein JWT vorhande -> token an request hängen
    else{
        var tokendata:Credentials = JSON.parse(token);
        request = request.clone({
            setHeaders: {
              Authorization: `Bearer ${tokendata.jwt}`
            }
          });
    }
    

    return next.handle(request);
  }
}