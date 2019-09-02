import { ITodoList } from '../interfaces/todo-list.interface';
import { ITaskCount } from '../interfaces/task-count.interface';

export class TodoList implements ITodoList {
    taskCount: ITaskCount;
    id: number;
    title: string;
    open: number;
    isNew: boolean;
    isEdit: boolean;
    isFavorite: boolean;
    icon: string;
}