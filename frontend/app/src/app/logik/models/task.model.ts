import { ITask } from '../interfaces/task.interface';

export class Task implements ITask{
    isNew: boolean;
    id: number;    
    title: string;
    description: string;
    priority: number;
    dateCreated: Date;
    dateCompleted: Date;
    dateDeadline: Date;
    state: number;
}