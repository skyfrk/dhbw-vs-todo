import { Injectable } from '@angular/core';
import { TodoList } from '../../models/todo-list.model';
import { ITodoList } from '../../interfaces/todo-list.interface';
import { listLazyRoutes } from '@angular/compiler/src/aot/lazy_routes';
import { HttpClient } from '@angular/common/http';
import { config } from 'rxjs';
import { AppConfig } from 'src/app/config/app.config';

@Injectable({
  providedIn: 'root'
})
export class FavoriteService {

  public Items: ITodoList[] = [];

  constructor(public http: HttpClient) {
    //Alle favoriten vom Server laden
    this.Refresh();
  }

  public Refresh() {
    this.http.get<ITodoList[]>(AppConfig.settings.apiServer.route + "/taskLists?limit=100&offset=0&is_favorite=true").subscribe(o => {
      this.Items = o;
    }, e => console.log(e));
  }

  public Edit(item: ITodoList) {
    var edit = -1;
    for (var i = 0; i < this.Items.length; i++) {
      if (this.Items[i].id == item.id) {
        edit = i;
      }
    }

    if (edit != -1) {
      this.Items.splice(edit, 1, item);
    }

  }

  public Add(List: ITodoList) {
    List.isFavorite = true;
    this.Items.push(List);
  }

  public Remove(List: ITodoList) {
    List.isFavorite = false;
    var test = this.Items.findIndex(o => o.id == List.id);
    this.Items.splice(test, 1);
  }

  public Toggle(item: ITodoList) {
    if (item.isFavorite) {
      this.Remove(item);
    }
    else {
      this.Add(item);
    }

    this.http.put(AppConfig.settings.apiServer.route + "/taskLists/" + item.id, item).subscribe(e => {
    }, o => {
    });
  }
}
