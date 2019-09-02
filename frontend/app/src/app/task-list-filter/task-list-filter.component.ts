import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-task-list-filter',
  templateUrl: './task-list-filter.component.html',
  styleUrls: ['./task-list-filter.component.scss']
})
export class TaskListFilterComponent implements OnInit {

  public filterASC: boolean = false;

  constructor(public route: Router, public active: ActivatedRoute) {
    this.active.queryParams.subscribe(p => {
      this.name = p.title;
      if (p.sortBy != undefined) {
        if (p.sortBy == "title") {
          this.orderBy = 1;
        }
        else if (p.sortBy == "date_created") {
          this.orderBy = 2
        }

        if (p.sortOrder == "asc") {
          this.filterASC = true;
        }
        else {
          this.filterASC = false;
        }
      }
    });
  }

  public name: string;
  public orderBy: number;

  ngOnInit() {
  }

  toggleASC() {
    this.filterASC = !this.filterASC;

    this.filter();
  }

  filter() {
    var params: any = {};
    if (this.name == undefined || this.name == "") {
    }
    else {
      params.title = this.name;
    }
    if (this.orderBy == undefined) {
    }
    else {
      if (this.orderBy == 1) {
        params.sortBy = "title";
      }
      else if (this.orderBy == 2) {
        params.sortBy = "date_created";
      }

      if (this.filterASC) {
        params.sortOrder = "asc"
      }
      else {
        params.sortOrder = "desc"
      }
    }
    this.route.navigate(["/lists"], { queryParams: params });
  }


}
