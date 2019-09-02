import { BrowserModule } from '@angular/platform-browser';
import { NgModule, APP_INITIALIZER } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http'
import { AppConfig } from './config/app.config';
import { MdlModule } from '@angular-mdl/core';
import { RegisterComponent } from './register/register.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { TokenInterceptor } from './logik/services/http-interceptor';
import { LoadingComponent } from './loading/loading.component';
import { AllTodosComponent } from './all-todos/all-todos.component';
import { InfoPageComponent } from './info-page/info-page.component';
import { IconSelectorComponent } from './icon-selector/icon-selector.component';
import { TodosComponent } from './todos/todos.component';
import { ConfirmationDialogComponent } from './confirmation-dialog/confirmation-dialog.component';
import { MdlDatePickerModule } from '@angular-mdl/datepicker';
import { MdlSelectModule } from '@angular-mdl/select';
import { MdlPopoverModule } from '@angular-mdl/popover';
import { AddButtonComponent } from './add-button/add-button.component';
import { TaskContentDialogComponent } from './task-content-dialog/task-content-dialog.component';
import { MdlFabMenuModule } from '@angular-mdl/fab-menu';
import { DashboardComponent } from './dashboard/dashboard.component';
import { InfoTileComponent } from './info-tile/info-tile.component';
import { MdlExpansionPanelModule } from '@angular-mdl/expansion-panel';
import { SearchBarComponent } from './search-bar/search-bar.component';
import { TaskListFilterComponent } from './task-list-filter/task-list-filter.component';
import { SettingsComponent } from './settings/settings.component';
import { TokenManagerComponent } from './token-manager/token-manager.component';

export function initializeApp(appConfig: AppConfig) {
  return () => appConfig.load();
}

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    LoadingComponent,
    AllTodosComponent,
    InfoPageComponent,
    IconSelectorComponent,
    TodosComponent,
    ConfirmationDialogComponent,
    AddButtonComponent,
    TaskContentDialogComponent,
    DashboardComponent,
    InfoTileComponent,
    SearchBarComponent,
    TaskListFilterComponent,
    SettingsComponent,
    TokenManagerComponent,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    AppRoutingModule,
    MdlModule,
    FormsModule,
    ReactiveFormsModule,
    MdlDatePickerModule,
    MdlPopoverModule,
    MdlSelectModule,
    MdlFabMenuModule,
    MdlExpansionPanelModule
  ],
  providers: [AppConfig, {
    provide: APP_INITIALIZER,
    useFactory: initializeApp,
    deps: [AppConfig], multi: true
  },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: TokenInterceptor,
      multi: true
    }],
  bootstrap: [AppComponent]
})
export class AppModule { }
