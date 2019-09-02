import { IReport } from '../interfaces/report.interface';
import { ITask } from '../interfaces/task.interface';

export class Report implements IReport {
    cancelledTaskCount: number;
    createdTaskCount: number;
    finishedTaskCount: number;
    inProgressTaskCount: number;
    openTaskCount: number;
    urgentTasks: ITask[];
}