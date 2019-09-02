import { ITask } from './task.interface';

export interface IReport {
    cancelledTaskCount: number;
    createdTaskCount: number;
    finishedTaskCount: number;
    inProgressTaskCount: number;
    openTaskCount: number;
    urgentTasks: ITask[];
}