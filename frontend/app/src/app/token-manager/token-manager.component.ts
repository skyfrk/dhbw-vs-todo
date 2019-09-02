import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AppConfig } from '../config/app.config';
import { IValidationToken } from '../logik/interfaces/validation-token.interface';

@Component({
  selector: 'app-token-manager',
  templateUrl: './token-manager.component.html',
  styleUrls: ['./token-manager.component.scss']
})
export class TokenManagerComponent implements OnInit {

  public Data: IValidationToken[] = [];

  constructor(private http: HttpClient) {
    this.getData();
  }

  getData() {
    this.http.get<IValidationToken[]>(AppConfig.settings.apiServer.route + "/user/tokens?limit=100&offset=0").subscribe(e => {
      this.Data = e;
    }, o => console.log(o));
  }

  ngOnInit() {
  }

  delete(passwd: string, token: IValidationToken) {
    this.http.post(AppConfig.settings.apiServer.route + "/user/token/" + token.id, { "password": passwd }).subscribe(e => {
      this.getData();
    }, f => console.log(f))
  }


}
