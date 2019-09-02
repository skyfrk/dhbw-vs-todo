import { Component, OnInit, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-confirmation-dialog',
  templateUrl: './confirmation-dialog.component.html',
  styleUrls: ['./confirmation-dialog.component.scss']
})
export class ConfirmationDialogComponent implements OnInit {

  @Output() Submit:EventEmitter<boolean> = new EventEmitter<boolean>();
  @Input() Text:string;

  @Input() IsVisible:boolean = false;
  @Output() IsVisibleChange:EventEmitter<boolean> = new EventEmitter<boolean>();

  constructor() { }

  ngOnInit() {
  }

  done(value:boolean){
    this.Submit.emit(value);
    this.IsVisible = false;
    this.IsVisibleChange.emit(this.IsVisible);
  }

}
