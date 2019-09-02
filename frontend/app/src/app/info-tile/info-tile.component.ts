import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-info-tile',
  templateUrl: './info-tile.component.html',
  styleUrls: ['./info-tile.component.scss']
})
export class InfoTileComponent implements OnInit {

  constructor() { }

  @Input()
  public title: string;

  @Input()
  public info: string;

  @Input()
  public icon: string;

  @Input()
  public smallScreen: boolean = false;

  ngOnInit() {
  }

}
