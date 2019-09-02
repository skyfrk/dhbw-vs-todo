import { Component, OnInit, EventEmitter, Input, Output } from '@angular/core';
import { ITodoList } from '../logik/interfaces/todo-list.interface';
import { HttpClient } from '@angular/common/http';
import { AppConfig } from '../config/app.config';

@Component({
  selector: 'app-icon-selector',
  templateUrl: './icon-selector.component.html',
  styleUrls: ['./icon-selector.component.scss']
})
export class IconSelectorComponent implements OnInit {

  @Input() SelectedValue: ITodoList;
  @Output() SelectedValueChange: EventEmitter<ITodoList> = new EventEmitter<ITodoList>();

  public Icons: string[] = [
    "cloud",
    "bug_report",
    "build",
    "label",
    "report_problem",
    "tab",
    "error",
    "notification_important",
    "airplay",
    "album",
    "visibility",
    "equalizer",
    "mic",
    "play_arrow",
    "radio",
    "pause",
    "call",
    "vpn_key",
    "add",
    "send",
    "access_alarm",
    "bluetooth",
    "insert_emoticon",
    "computer",
    "cloud",
    "attachment",
    "gamepad",
    "headset",
    "audiotrack",
    "colorize",
    "nature",
    "photo",
    "directions_walk",
    "hotel",
    "directions_car",
    "directions_bus",
    "directions_bike",
    "train",
    "tram",
    "android",
    "fitness_center",
    "golf_course",
    "beach_access",
    "hot_tub",
    "group",
    "school",
    "thumb_up_alt",
    "cake",
    "domain"
  ];

  constructor(private http: HttpClient) { }

  save(value: string) {
    this.SelectedValue.icon = value;

    this.http.put(AppConfig.settings.apiServer.route + "/taskLists/" + this.SelectedValue.id,
      this.SelectedValue).subscribe(s => {
        this.SelectedValue = undefined;
        this.SelectedValueChange.emit(this.SelectedValue);
      }, e => console.log(e));
  }

  close() {
    this.SelectedValue = undefined;
    this.SelectedValueChange.emit(this.SelectedValue);
  }

  ngOnInit() {
  }

}
