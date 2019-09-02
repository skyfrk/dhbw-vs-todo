import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { AllTodosComponent } from './all-todos/all-todos.component';
import { InfoPageComponent } from './info-page/info-page.component';
import { TodosComponent } from './todos/todos.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { SettingsComponent } from './settings/settings.component';


const routes: Routes = [
  {
    path: 'login', children: [
      { path: '', component: LoginComponent },
      { path: ':token', component: LoginComponent }
    ]
  },
  // { path: 'login', component: LoginComponent },
  // { path: 'register/confirm/:token', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'settings', component: SettingsComponent },
  { path: 'lists', component: AllTodosComponent },
  { path: 'about', component: InfoPageComponent },
  { path: 'todos/:id', component: TodosComponent },
  { path: 'todos', component: TodosComponent },
  {
    path: '', children: [
      { path: '', component: DashboardComponent },
      { path: 'index', component: DashboardComponent },
      { path: 'dashboard', component: DashboardComponent },
      { path: 'dashboard/list/:id', component: DashboardComponent }
    ]
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
