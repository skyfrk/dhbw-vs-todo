import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { AppConfig } from '../config/app.config';
import { ITask } from '../logik/interfaces/task.interface';
import { Task } from '../logik/models/task.model';
import { MdlDatePickerService } from '@angular-mdl/datepicker';
import { MdlTextFieldComponent } from '@angular-mdl/core';
import { MdlSelectComponent } from '@angular-mdl/select';
import { ApiServiceService } from '../logik/services/api-service/api-service.service';
import { FavoriteService } from '../logik/services/favorite-service/favorite.service';
import { IFilterParams } from '../logik/interfaces/filter-params.interface';
import { FilterParams } from '../logik/models/filter-params.mode';
import { QueryParams } from '../logik/models/query-params.model';

@Component({
  selector: 'app-todos',
  templateUrl: './todos.component.html',
  styleUrls: ['./todos.component.scss']
})
export class TodosComponent implements OnInit {
  AllTodods: ITask[] = [];
  public selectedDate: Date = new Date();

  public isReadOnly = false;

  public mobileSize = 560;

  private _taskListId: number;

  private ShowAltIcon = false;

  get TaskListId() {
    return this._taskListId;
  }

  set TaskListId(v: any) {
    this._taskListId = v.replace(/\s/g, "");;
  }

  public win: any;

  public sliderValue = 0;

  public markWithId: number = -1;

  public queryParams: IFilterParams = new FilterParams();

  constructor(private router: Router, private route: ActivatedRoute, private http: HttpClient, private datePicker: MdlDatePickerService,
    public service: ApiServiceService, private favoriteSer: FavoriteService) {
    this.service.ShowLoading = true;

    this.win = window;

    route.queryParams.subscribe(o => {

      if (o["sel"]) {
        this.markWithId = o["sel"];
      }

      this.queryParams = new FilterParams();


      for (var param in o) {
        var set = false;

        switch (param) {
          case "sort_by":
          case "sort_order":
          case "title":
          case "state":
          case "priority":
            set = true;
            break;
        }
        if (set) {
          this.queryParams[param] = o[param];
        }
      }

      this.getData();

    });



    //Tasks laden

  }

  getData() {
    this.route.params.subscribe(o => {
      if (o.id == undefined) {
        this.http.get<ITask[]>(AppConfig.settings.apiServer.route + "/tasks", { params: QueryParams.convertToHttpParams(this.queryParams) }).subscribe(e => {
          this.AllTodods = [];
          e.forEach(i => {
            this.AllTodods.push(i);
          });
          this.service.ShowLoading = false;
          this.isReadOnly = true;
        }, o => console.log(o))
      }
      else {
        this.TaskListId = o.id;
        this.http.get<ITask[]>(AppConfig.settings.apiServer.route + "/taskLists/" + this.TaskListId + "/tasks", { params: QueryParams.convertToHttpParams(this.queryParams) }).subscribe(e => {
          this.AllTodods = [];
          e.forEach(i => {
            this.AllTodods.push(i);
          });
          this.service.ShowLoading = false;
        }, o => {
          console.log(o);
          this.service.ShowLoading = false;
        });
      }
    });
  }

  addNewTask(title: MdlTextFieldComponent, desc: MdlTextFieldComponent, prio: MdlSelectComponent) {


    if (title.value == undefined || title.value == "") {
      return;
    }
    if (desc.value == undefined || desc.value == "") {
      return;
    }
    if (prio.ngModel == undefined || prio.ngModel == "") {
      return;
    }

    this.ShowAltIcon = false;
    var task1 = new Task();
    task1.description = desc.value;
    task1.priority = prio.ngModel;
    task1.title = title.value;
    task1.dateDeadline = this.selectedDate;

    this.http.post<ITask>(AppConfig.settings.apiServer.route + "/taskLists/" + this.TaskListId + "/tasks", task1).subscribe(e => {
      this.AllTodods.push(e);
      this.favoriteSer.Refresh();
    }, o => {
      console.log(o);
    })


    desc.value = undefined;
    prio.ngModel = undefined;
    prio.text = "";
    title.value = undefined
    this.selectedDate = new Date();

    var idx = undefined;
    for (let i = 0; i < this.AllTodods.length; i++) {
      const element = this.AllTodods[i];
      if (element.isNew) {
        idx = i;
        break;
      }
    }

    this.AllTodods.splice(idx, 1);
  }

  ngOnInit() {
  }

  addEmptyTask() {
    if (this.cancelNew()) {
      this.ShowAltIcon = false;
      return;
    }
    var block = false;

    this.AllTodods.forEach(o => {
      if (o.isNew != undefined && o.isNew == true) {
        block = true;
      }
    });

    if (!block) {
      var t = new Task();
      t.isNew = true;
      t.state = 0;
      t.priority = 1;
      t.dateDeadline = new Date();
      this.AllTodods.unshift(t);
    }

    this.ShowAltIcon = true;
    setTimeout(() => this.focus(), 200);
  }

  focus() {
    var test = document.getElementsByTagName("input");
    for (var i = 0; i < test.length; i++) {
      var holder = test[i];
      if (holder.id == "title-input") {
        test[i].scrollIntoView();
        test[i].focus();
      }
    }
  }



  public pickADate($event: MouseEvent) {
    this.datePicker.selectDate(this.selectedDate, { openFrom: $event }).subscribe((selectedDate: Date) => {
      this.selectedDate = selectedDate //? moment(selectedDate) : null;
    });
  }

  saveItem(t: ITask) {
    var edit = undefined;
    this.http.put<ITask>(AppConfig.settings.apiServer.route + "/tasks/" + t.id, t).subscribe(e => {
      this.favoriteSer.Refresh();
    }, o => {
      console.log(o);

      //Wenn die aktion verboten ist (z.B. kann ein Task nicht von finished auf cancelled geschoben werden)
      //-> Stand von Server laden und alten ersetzen 
      for (var i = 0; i < this.AllTodods.length; i++) {
        if (this.AllTodods[i].id == t.id) {
          edit = i;
          break;
        }
      }
      if (edit != undefined) {
        this.http.get<ITask>(AppConfig.settings.apiServer.route + "/tasks/" + t.id).subscribe(g => {
          this.AllTodods.splice(edit, 1, g);
        }, z => console.log(z));
      }
    });
  }

  changeItemState(state: number, item: ITask) {
    item.state = state;
    this.saveItem(item);
  }

  cancelNew() {
    var idxNew = -1;
    for (var i = 0; i < this.AllTodods.length; i++) {
      if (this.AllTodods[i].isNew) {
        idxNew = i;
      }
    }

    if (idxNew != -1) {
      this.AllTodods.shift();
    }

    return idxNew != -1;
  }

}
