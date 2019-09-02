import { Component, OnInit } from '@angular/core';
import { IRegisterData } from '../logik/interfaces/register.interface';
import { RegisterData } from '../logik/models/register.model';
import { NgForm, FormGroup, FormControl, FormBuilder } from '@angular/forms';
import { ApiServiceService } from '../logik/services/api-service/api-service.service';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { AppConfig } from '../config/app.config';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {

  public form: FormGroup;
  public name = new FormControl('');
  public email = new FormControl('');
  public passwd = new FormControl('');
  public passwdConfirm = new FormControl('');

  public nameInvalid = false;
  public emailInvalid = false;
  public passwdInvalid = false;
  public passwdConfirmInvalid = false;

  constructor(private service: HttpClient, private router: Router, private apiservice: ApiServiceService, private fb: FormBuilder) {

    this.form = fb.group({
      'name': this.name,
      'mail': this.email,
      'passwd': this.passwd,
      'passwdConfirm': this.passwdConfirm
    });

    this.apiservice.loginChanged.subscribe((f: boolean) => {
      if (f) {
        this.router.navigate(["/"]);
      }
    });

    if (this.apiservice.IsLoggedIn) {
      this.router.navigate(["/"]);

    }
  }

  public registerFailed = false;
  public dataSend = false;
  public Info: string = "";

  ngOnInit() {
  }

  onSubmit() {
    var block: boolean = false;

    var rgx = new RegExp(/^[a-zA-Z]\w{7,19}$/);
    var test = rgx.test(this.passwd.value);
    if (!test) {
      block = true;
      this.passwdInvalid = true;
    }
    else {
      this.passwdConfirmInvalid = false;
    }

    if (this.passwd.value != this.passwdConfirm.value) {
      block = true;
      this.passwdConfirmInvalid = true;
    }
    else {
      this.passwdConfirmInvalid = false;
    }

    // exp = new RegExp("")
    if (this.name.value == undefined || !this.name.value.match(/.{5,}/)) {
      block = true;
      this.nameInvalid = true;
    }
    else {
      this.nameInvalid = false;
    }

    if (this.email.value == undefined || !this.email.value.match(/.+\@.+\..+/)) {
      block = true;
      this.emailInvalid = true;
    }
    else {
      this.emailInvalid = false;
    }

    if (block) {
      return;
    }

    var registerData: IRegisterData = new RegisterData;
    registerData.displayName = this.name.value;
    registerData.mail = this.email.value;
    registerData.password = this.passwd.value;

    // this.mail = registerData.mail;
    //Daten an Server senden 
    this.service.post<Object>(AppConfig.settings.apiServer.route + "/register", registerData).subscribe(e => {
      console.log(e);
      this.dataSend = true;
    }, f => {
      //Todo:Errorhandling
      console.log(f);
      this.registerFailed = true;
    });

  }
}
