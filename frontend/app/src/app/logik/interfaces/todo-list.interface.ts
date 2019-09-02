import { ITaskCount } from './task-count.interface';

export interface ITodoList {
    id: number;
    title: string; //Title
    open: number;
    isNew: boolean;
    isEdit: boolean;
    isFavorite: boolean;
    icon: string;
    taskCount: ITaskCount;
}