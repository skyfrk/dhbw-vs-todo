import { ITaskCount } from '../interfaces/task-count.interface';

export class TaskCount implements ITaskCount{
    id:number
    aborted: number;    
    done: number;
    inProgress: number;
    open: number;
    total: number;
}