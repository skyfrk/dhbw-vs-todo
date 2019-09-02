export interface ITask{
        id:number,
        title:string,
        description:string,
        priority:number,
        dateCreated: Date,
        dateCompleted: Date,
        dateDeadline: Date,
        state: number
        isNew:boolean;
}