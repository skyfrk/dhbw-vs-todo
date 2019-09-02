import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { MdlBackdropOverlayComponent } from '@angular-mdl/core';

@Component({
  selector: 'app-search-bar',
  templateUrl: './search-bar.component.html',
  styleUrls: ['./search-bar.component.scss']
})
export class SearchBarComponent implements OnInit {

  public param: any = {};

  public isOpen: boolean = false;

  public filterASC: boolean = false;
  public filterBy;

  public filterValue: any = {};

  constructor(public router: Router, public activated: ActivatedRoute) {
    this.activated.queryParams.subscribe(o => {
      switch (o.sort_order) {
        case "desc":
          this.filterASC = false;
          break;
        case "asc":
          this.filterASC = true;
          break;
      }

      switch (o.sort_by) {
        case "tasklist":
          this.filterBy = 1;
          break;
        case "priority":
          this.filterBy = 2;
          break;
        case "state":
          this.filterBy = 3;
          break;
        default:
          this.filterBy = undefined;
          break;
      }

      this.filterValue = {};

      for (var param in o) {
        var set = false;

        switch (param) {
          case "title":
          case "state":
          case "priority":
            set = true;
            break;
        }
        if (set) {
          this.filterValue[param] = o[param];
        }
      }
    });
  }

  public SeachParam: any = {};

  ngOnInit() {
  }

  toggle() {
    this.isOpen = !this.isOpen;
  }

  toggleASC() {
    this.filterASC = !this.filterASC;
    this.sort();
  }

  sort() {
    if (this.filterASC) {
      this.param.sort_order = "asc";
    }
    else {
      this.param.sort_order = "desc";
    }

    switch (this.filterBy) {
      case 1:
        this.param.sort_by = "tasklist";
        break;
      case 2:
        this.param.sort_by = "priority";
        break;
      case 3:
        this.param.sort_by = "state";
        break;
      default:
        this.param.sort_by = undefined;
        break;
    }
    this.navigate();
  }

  navigate() {
    if (this.param.sort_by != undefined && this.param.sort_by != "") {
      this.router.navigate([], { queryParams: this.param, relativeTo: this.activated });
    }
    else {
      this.param.sort_by = undefined;
      this.param.sort_order = undefined;
      this.router.navigate([], { queryParams: this.param, relativeTo: this.activated });
    }
  }

  filter() {
    for (var item in this.filterValue) {
      if (this.filterValue[item] === "") {
        this.filterValue[item] == undefined;
      }
      this.param[item] = this.filterValue[item];
    }
    this.navigate();
  }
}
