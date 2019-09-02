import { Injectable, EventEmitter } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AppConfig } from 'src/app/config/app.config';
import { AppRoutingModule } from 'src/app/app-routing.module';
import { Router } from '@angular/router';
import { interval } from 'rxjs';
import { Credentials } from '../../interfaces/credentials.interface';

@Injectable({
  providedIn: 'root'
})
//Beinhaltet alle wichtigen Daten und Methoden
export class ApiServiceService {
  //Benutzer ist angemeldet
  private isLoggedIn: boolean = false;
  //Benutzername
  public displayName: string;
  //Credentials
  public jwt: Credentials;
  //Loginstatus wird geändert (z.B. durch logout)
  public loginChanged: EventEmitter<boolean> = new EventEmitter<boolean>();

  //Alle wichtigen Parameter wurden geladen und die App ist einsatzbereit
  public initialized = false;

  public ShowLoading = false;


  public get IsLoggedIn(): boolean {
    return this.isLoggedIn;
  }

  public set IsLoggedIn(value) {
    this.isLoggedIn = value;
    this.loginChanged.emit(value);
  }

  constructor(private http: HttpClient, private router: Router) {
    this.verifyTokenFirst();
  }

  verifyToken() {
    //Vorhandener Token validieren
    this.http.post(AppConfig.settings.apiServer.route + "/login/renew", "").subscribe(o => {
      //Token in Ordnung -> menü zeigen
      this.IsLoggedIn = true;
      this.initialized = true;

      localStorage.setItem("jwt", JSON.stringify(o));
    }, e => {
      //Token abgelaufen -> zum Login
      console.log(e);
      this.IsLoggedIn = false;
      this.initialized = true;
      this.redirectToLogin();
    });

  }

  redirectToLogin() {
    //Ist der Benutzer bereits auf einer der Anmeldeseiten -> login oder register
    //muss er nicht redirected werden
    if (this.router.url.startsWith("/login") || this.router.url.startsWith("/register")
      || this.router.url.startsWith("/about")) {

    }
    else {
      this.router.navigate(["/login"]);
    }
  }

  verifyTokenFirst() {
    this.verifyToken();
    interval(900000).subscribe(o => this.verifyToken);
  }

}
