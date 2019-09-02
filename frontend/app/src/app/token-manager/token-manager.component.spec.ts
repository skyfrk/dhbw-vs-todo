import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TokenManagerComponent } from './token-manager.component';

describe('TokenManagerComponent', () => {
  let component: TokenManagerComponent;
  let fixture: ComponentFixture<TokenManagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TokenManagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TokenManagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
