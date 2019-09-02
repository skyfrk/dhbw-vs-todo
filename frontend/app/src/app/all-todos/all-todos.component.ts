import { Component, OnInit, ViewChild, ElementRef, HostListener } from '@angular/core';
import { ITodoList } from '../logik/interfaces/todo-list.interface';
import { TodoList } from '../logik/models/todo-list.model';
import { FavoriteService } from '../logik/services/favorite-service/favorite.service';
import { AppConfig } from '../config/app.config';
import { HttpClient } from '@angular/common/http';
import { Router, ActivatedRoute } from '@angular/router';
import { createHostListener } from '@angular/compiler/src/core';
import { TaskCount } from '../logik/models/task-count.model';
import { ITask } from '../logik/interfaces/task.interface';
import { MdlTextFieldComponent, MdlDialogService } from '@angular-mdl/core';
import { ApiServiceService } from '../logik/services/api-service/api-service.service';
import { timer } from 'rxjs';

@Component({
  selector: 'app-all-todos',
  templateUrl: './all-todos.component.html',
  styleUrls: ['./all-todos.component.scss']
})
export class AllTodosComponent implements OnInit {

  public data: ITodoList[] = [];
  public dataSave: ITodoList[] = [];

  private offset = 0;
  private limit = 100;

  public IsDialogeVisible = false;

  public SelectedItem: ITodoList;

  public titleFilter: string = undefined;
  public orderBy: string = undefined;
  public orderDir: string = undefined;


  @HostListener('window:keydown', ['$event'])
  onKeyDown(event) {

    if (event.keyCode == 13) {
      var test = document.getElementsByTagName("input");
      for (var i = 0; i < test.length; i++) {
        if (test[i].id == "iName ") {
          this.save(test[i].value);
          return;
        }
      }
    }
  }

  constructor(public favorite: FavoriteService, public http: HttpClient, public router: Router, public service: ApiServiceService, public active: ActivatedRoute,
    public dialog: MdlDialogService) {
    service.ShowLoading = true;

    this.active.queryParams.subscribe(p => {
      if (p.title == undefined || p.title == "") {
        this.titleFilter = undefined;
      }
      else {
        this.titleFilter = p.title;
      }

      if (p.sortBy == undefined) {
        this.orderBy = undefined;
        this.orderDir = undefined;
      }
      else {
        this.orderBy = p.sortBy;
        this.orderDir = p.sortOrder;
      }
      this.loadAllData();
    });

    document.addEventListener("click", e => this.closeContextMenu());
  }

  loadAllData() {

    var append = "";
    if (this.titleFilter != undefined) {
      append = "&title=" + this.titleFilter;
    }

    if (this.orderBy != undefined) {
      append += "&sort_by=" + this.orderBy + "&sort_order=" + this.orderDir;
    }

    this.http.get<ITodoList[]>(AppConfig.settings.apiServer.route + "/taskLists?limit=" + this.limit + "&offset=" + this.offset + append).subscribe(e => {
      this.data = e;
      this.service.ShowLoading = false;
    }, o => {
      console.log(o);
      this.service.ShowLoading = false;
    });
  }

  favoriteItem(item: ITodoList, event: MouseEvent) {
    this.favorite.Toggle(item);
  }


  ngOnInit() {
  }

  removeEmptyList() {

  }

  addNewList() {

    var block = false;
    for (var i = 0; i < this.data.length; i++) {
      if (this.data[i].isNew) {
        block = true;
      }
    }

    if (!block) {
      var a = new TodoList();
      a.isNew = true;
      a.isEdit = false;
      a.title = "";
      a.taskCount = new TaskCount();
      a.taskCount.open = 0;
      a.isFavorite = false;
      a.icon = "alarm";
      this.data.push(a);
    }

    setTimeout(() => { this.focus(); }, 200);
  }

  focus() {
    var test = document.getElementsByTagName("input");
    for (var i = 0; i < test.length; i++) {
      var holder = test[i];
      if (holder.id == "iName ") {
        test[i].scrollIntoView();
        test[i].focus();
      }
    }
  }

  focusNoScroll() {
    var test = document.getElementsByTagName("input");
    for (var i = 0; i < test.length; i++) {
      var holder = test[i];
      if (holder.id == "iName ") {
        test[i].scrollIntoView();
        test[i].focus();
      }
    }
  }

  setEdit(item: ITodoList) {
    item.isEdit = true;
    item.isNew = false;

    setTimeout(() => { this.focus(); }, 100);
  }

  loseFocus(param: any) {
    var value = param.currentTarget.value;
    // this.save(value);
  }

  save(value: string) {
    var remove = [];
    for (var i = 0; i < this.data.length; i++) {
      var el = this.data[i];

      if (el.isNew) {
        remove.push(el);

        if (value == "") {
        }
        else {
          el.isNew = false;
          el.isEdit = false;
          el.title = value;

          //el an Server schiken und 
          this.http.post<TodoList>(AppConfig.settings.apiServer.route + "/taskLists", el).subscribe(o => {
            this.data.push(o);
            this.dataSave.push(o);
          },
            e => {
              console.log(e);
            });
        }
      }
      else if (el.isEdit) {
        this.saveChanges(el, value);
      }
    }

    remove.forEach(el => {
      var t = this.data.indexOf(el);
      this.data.splice(t, 1);
    });
  }

  saveChanges(list: ITodoList, value: string) {
    list.title = value;

    if (list.isFavorite) {
      this.favorite.Edit(list);
    }

    this.http.put<ITodoList>(AppConfig.settings.apiServer.route + "/taskLists/" + list.id, list).subscribe(e => {
      list.isEdit = false;
      list.isNew = false;
      console.log(e);
    }, o => console.log(o));
  }

  showTasks(list: ITodoList) {
    console.log(list);
    this.router.navigate(["/todos/" + list.id]);
  }

  private ItemToDelete: ITodoList;
  delete(value: ITodoList) {
    // this.ItemToDelete = value;
    // this.IsDialogeVisible = true;

    let result = this.dialog.confirm("Wollen Sie die Aufgabe '" + value.title + "' wirklich löschen", "Nein", "Ja");

    result.subscribe(() => {
      this.deleteFinal(value);
    })
  }

  deleteFinal(value: ITodoList) {
    if (value.isFavorite) {
      this.favorite.Remove(value);
    }
    this.http.delete(AppConfig.settings.apiServer.route + "/taskLists/" + value.id).subscribe(e => {
      console.log(e);
      this.loadAllData();
    }, f => {
      console.log(f);
    });
  }

  search(value: string) {
    //Sortieren über backend laufen lassen
    var item: ITodoList[] = [];

    this.dataSave.forEach(o => {
      if (o.title.toUpperCase().startsWith(value.toUpperCase())) {
        item.push(o);
      }
    });
    this.dataSave.forEach(o => {
      if (o.title.toUpperCase().includes(value.toUpperCase())) {
        var add = true;
        item.forEach(i => {
          if (i.id == o.id) {
            add = false;
          }
        });

        if (add) {
          item.push(o);
        }
      }
    });

    this.data = item;
  }

  callIconSelector(item: ITodoList) {
    if (this.SelectedItem == item) {
      this.SelectedItem = undefined;
    }

    this.SelectedItem = item;
  }

  public contextMenuActive: boolean = false;
  public menuTop: string;
  public menuLeft: string;

  public contextItem: ITodoList = new TodoList();

  toggleMenuOn(param: any, item) {
    this.contextItem = item;
    param.preventDefault();
    this.contextMenuActive = true;
    this.positionMenu(param);
  }

  public positionMenu(param: any) {
    var clickCoords = this.getPosition(param)

    var clickCoordsX = clickCoords.x;
    var clickCoordsY = clickCoords.y;

    var menuWidth = 245;
    var menuHeight = 135;

    var windowWidth = window.innerWidth;
    var windowHeight = window.innerHeight;

    if ((windowWidth - clickCoordsX) < menuWidth) {
      this.menuLeft = windowWidth - menuWidth + "px";
    } else {
      this.menuLeft = clickCoordsX + "px";
    }

    if ((windowHeight - clickCoordsY) < menuHeight) {
      this.menuTop = windowHeight - menuHeight + "px";
    } else {
      this.menuTop = clickCoordsY + "px";
    }
  }

  public getPosition(e: MouseEvent) {
    var posx = 0;
    var posy = 0;

    // if (!e) var e = window.event;

    if (e.pageX || e.pageY) {
      posx = e.pageX;
      posy = e.pageY;
    } else if (e.clientX || e.clientY) {
      posx = e.clientX + document.body.scrollLeft +
        document.documentElement.scrollLeft;
      posy = e.clientY + document.body.scrollTop +
        document.documentElement.scrollTop;
    }

    return {
      x: posx,
      y: posy
    }
  }

  setEditMenu() {
    this.contextItem.isEdit = true;
    this.contextItem.isNew = false;

    setTimeout(() => { this.focusNoScroll(); }, 100);
  }

  deleteMenu() {
    this.delete(this.contextItem);
  }

  closeContextMenu() {
    this.contextMenuActive = false;
    this.contextItem = new TodoList();
  }

  showDashboard() {
    this.router.navigate(["/dashboard/list/" + this.contextItem.id])
  }
}
