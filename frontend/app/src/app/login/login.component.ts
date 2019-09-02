import { Component, OnInit } from '@angular/core';
import { AppConfig } from '../config/app.config';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Router, ActivatedRoute } from '@angular/router';
import { ApiServiceService } from '../logik/services/api-service/api-service.service';
import { slideInAnimation } from '../logik/animation';
import { FavoriteService } from '../logik/services/favorite-service/favorite.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  public keep: boolean = false;
  public info = "";
  public confirmed = false;

  constructor(private service: HttpClient, private router: Router,
    private apiservice: ApiServiceService, private route: ActivatedRoute, private fav: FavoriteService) {

    this.route.params.subscribe(params => {
      //Wurde der Parameter token in der ulr definiert
      if (params.token != undefined) {
        //token an API senden und Benutzer zeigen, dass er sich anmelden kann
        this.service.get(AppConfig.settings.apiServer.route + "/register/confirm/" + params.token).subscribe(e => {
          this.confirmed = true;
        }, f => {
          console.log(f);
        })
      }
    });

    if (this.apiservice.IsLoggedIn) {
      this.router.navigate(["/"]);

    }
  }

  ngOnInit() {
  }

  login(data: any) {
    //Benutzername und Passwort an Server senden

    //Wenn keep == true -> Servertoken im localStorage speichern 
    //Wenn keep == false -> Servertoken im sessionStorage speichern
    //             geht nach schließen d. Browsers verloren

    if (data.rememberMe == undefined) {
      data.rememberMe = false;
    }

    this.service.post<Object>(AppConfig.settings.apiServer.route + "/login", data.value).subscribe(o => {
      var json = JSON.stringify(o);
      if (data.value.rememberMe) {
        localStorage.setItem("jwt", json);
      }
      else {
        sessionStorage.setItem("jwt", json);
      }
      this.apiservice.IsLoggedIn = true;
      this.fav.Refresh();
      this.router.navigate(["/"]);
    }, e => {
      console.log(e);
      if (e.status == 403) {
        this.info = "Der Account wurde noch nicht aktiviert";
      }
      else if (e.status == 401) {
        this.info = "Benutzername oder Passwort falsch";
      }
      else {
        this.info = "Benutzername oder Passwort falsch"
      }
    });
  }

}
