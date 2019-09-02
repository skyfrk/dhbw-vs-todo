import { Component, OnInit, EventEmitter, Output, Input } from '@angular/core';

@Component({
  selector: 'app-add-button',
  templateUrl: './add-button.component.html',
  styleUrls: ['./add-button.component.scss']
})
export class AddButtonComponent implements OnInit {

  @Output()
  public buttonClick: EventEmitter<MouseEvent> = new EventEmitter<MouseEvent>();

  @Input()
  public AltIcon: string;
  @Input()
  public ShowAltIcon: boolean;

  constructor() { }

  ngOnInit() {
  }

}
