import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ApiServiceService } from '../logik/services/api-service/api-service.service';
import { AppConfig } from '../config/app.config';
import { IReport } from '../logik/interfaces/report.interface';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {

  private _days = 7;
  public reportData: IReport;

  get linkTo() {

    if (this.DashboardID != undefined) {
      return "/todos/" + this.DashboardID;
    }
    else {
      return "../todos";
    }
  }

  public TimeoutHandle;

  public DashboardID;

  get days() {
    return this._days;
  }

  set days(v) {
    this._days = v;

    if (this.TimeoutHandle != undefined) {
      window.clearTimeout(this.TimeoutHandle);
    }

    this.TimeoutHandle = window.setTimeout(() => {
      // this.api.ShowLoading = true;
      this.getData();
    }, 500);

  }

  constructor(public http: HttpClient, public api: ApiServiceService, public route: ActivatedRoute) {
    this.api.ShowLoading = true;
    this.getData();
  }


  getData() {

    this.route.params.subscribe(e => {
      if (e.id != undefined && e.id != "") {

        this.DashboardID = e.id;

        this.http.get<any>(AppConfig.settings.apiServer.route + "/taskLists/" + this.DashboardID + "/report?days=" + this.days).subscribe(e => {
          this.reportData = e;
          console.log(this.reportData);
          this.api.ShowLoading = false;
        },
          o => {
            console.log(o);
          });
      }
      else {
        this.http.get<any>(AppConfig.settings.apiServer.route + "/report?days=" + this.days).subscribe(e => {
          this.reportData = e;
          console.log(this.reportData);
          this.api.ShowLoading = false;
        },
          o => {
            console.log(o);
          });
      }
    });

  }

  ngOnInit() {

  }
}
