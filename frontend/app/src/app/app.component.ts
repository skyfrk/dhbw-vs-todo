import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { RouterModule, Router } from '@angular/router';
import { ApiServiceService } from './logik/services/api-service/api-service.service';
import { FavoriteService } from './logik/services/favorite-service/favorite.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  title = 'xOrga';

  constructor(private http: HttpClient, private router: Router, public service: ApiServiceService,
    public favorite: FavoriteService) {
  }


  ngOnInit() {
  }

  logout() {
    //Credentials aus localstorage löschen und benutzer abmelden
    localStorage.clear();
    sessionStorage.clear();
    this.service.IsLoggedIn = false;
    this.router.navigate(["/login"]);
  }
}
