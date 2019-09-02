import { ILoginData } from '../interfaces/login.interface';

export class LoginData implements ILoginData{
    mail: string;    
    password: string;
    rememberMe:boolean;
}