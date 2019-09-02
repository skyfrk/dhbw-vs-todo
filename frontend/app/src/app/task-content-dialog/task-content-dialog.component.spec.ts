import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TaskContentDialogComponent } from './task-content-dialog.component';

describe('TaskContentDialogComponent', () => {
  let component: TaskContentDialogComponent;
  let fixture: ComponentFixture<TaskContentDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TaskContentDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TaskContentDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
