import { IRegisterData } from '../interfaces/register.interface';

export class RegisterData implements IRegisterData{
    mail: string;
    displayName: string;    
    password: string;
}