# API

## Routes

### Register

| Method | Route             | Request Body                      | Response body | Description                                          |
| ------ | ----------------- | --------------------------------- | ------------- | ---------------------------------------------------- |
| POST   | /register         | [Body](#POST-register-Body)       | -             | User registrieren                                    |
| POST   | /register/resend  | [Body](#POST-registerresend-Body) | -             | Registrierungsbestätigungsmail nochmal senden lassen |
| GET    | /register/confirm | -                                 | -             | Registrierung bestätigen                             |

### Login

| Method | Route        | Request Body             | Response body                         | Description     |
| ------ | ------------ | ------------------------ | ------------------------------------- | --------------- |
| POST   | /login       | [Body](#POST-login-Body) | [Response](#POST-login-Response)      | JWT holen       |
| POST   | /login/renew | -                        | [Response](#POST-loginrenew-Response) | Neuen JWT holen |

### User

| Method | Route                                 | Request Body                   | Response body                    | Description                       |
| ------ | ------------------------------------- | ------------------------------ | -------------------------------- | --------------------------------- |
| GET    | /user/tokens?limit=`int`&offset=`int` | -                              | [Response](#POST-login-Response) | Liste aktiver JWT für den Account |
| POST   | /user/token/{id}                      | [Body](#POST-usertokenid-Body) | -                                | Aktives Token invalidieren        |

### Tasks

| Method | Route                           | Request Body            | Response body                 | Description                |
| ------ | ------------------------------- | ----------------------- | ----------------------------- | -------------------------- |
| GET    | /tasks?limit=`int`&offset=`int` | -                       | JSON array of  [tasks](#task) | Alle Aufgaben lesen        |
| GET    | /tasks/{id}                     | -                       | [task](#task)                 | Aufgabe lesen              |
| PUT    | /tasks/{id}                     | [Body](#put-tasks-body) | [taskList](#tasklist)         | Aufgabe (partiell) updaten |
| DELETE | /tasks/{id}                     | -                       | [taskList](#tasklist)         | Aufgabe löschen            |

### Tasklists

| Method | Route                                          | Request Body                    | Response body                        | Description                               |
| ------ | ---------------------------------------------- | ------------------------------- | ------------------------------------ | ----------------------------------------- |
| GET    | /taskLists?limit=`int`&offset=`int`            | -                               | JSON array of [taskLists](#tasklist) | Liefert alle Aufgabenlisten               |
| POST   | /taskLists                                     | [Body](#postput-tasklists-body) | [taskList](#tasklist)                | Erstellt neue Aufgabenliste               |
| GET    | /taskLists/{id}                                | -                               | [taskList](#tasklist)                | Aufgabenliste lesen                       |
| PUT    | /taskLists/{id}                                | [Body](#postput-tasklists-body) | [taskList](#tasklist)                | Aufgabenliste (partiell) updaten          |
| DELETE | /taskLists/{id}                                | -                               | [taskList](#tasklist)                | Aufgabenliste löschen                     |
| GET    | /taskLists/{id}/tasks?limit=`int`&offset=`int` | -                               | JSON array of  [tasks](#task)        | Tasks einer Aufgabenliste lesen           |
| POST   | /taskLists/{id}/tasks                          | [Body](#post-tasks-body)        | [task](#task)                        | Neuer Task in einer Aufgabenliste anlegen |
| GET    | /taskLists/{id}/report?days=`int`              | -                               | [report](#report)                    | Report für die taskList                   |

### Report

| Method | Route              | Request Body | Response body     | Description                        |
| ------ | ------------------ | ------------ | ----------------- | ---------------------------------- |
| GET    | /report?days=`int` | -            | [report](#report) | Report über die `int` letzten Tage |

## Request Bodies

### POST login Body

```json
{
    "mail": "si-mon@posteo.ru",
    "password": "sUperdupersecret13!",
    "rememberMe": true
}
```

### POST register Body

```json
{
    "mail": "si-mon@posteo.ru",
    "displayName": "somedude13",
    "password": "sUperdupersecret13!"
}
```

### POST register/resend Body

```json
{
    "mail": "si-mon@posteo.org"
}
```

### POST user/token/id Body

```json
{
    "password": "sUperdupersecret13!"
}
```

### POST taskLists Body

```json
{
    "title": "Some title",
    "icon": "someiconname"
}
```

### PUT taskLists Body

```json
{
    "title": "Some title",
    "icon": "someiconname",
    "isFavorite": true // optional
}
```

### POST/PUT tasks Body

```json
{
    "title": "Some task title",
    "description": "some task description", // optional
    "priority": 4, // optional
    "dateDeadline": "serialized php DateTime object", // optional
    "state": "1"
}
```

## Response Bodies

### POST login Response

* displayName: string
* jwt: string

### POST login/renew Response

* jwt: string

### GET user/tokens Response

Array of:

* id: int (to use for DELETE /user/token/{id})
* dateCreated: DateTime (to display)
* dateLastUsed: DateTime (to display)

### taskList

* id: int
* title: string
* isFavorite: boolean
* icon: string
* dateCreated: DateTime
* taskCount
  * total: int
  * open: int
  * inProgress: int
  * done: int
  * aborted: int

### task

* id: int
* title: string
* description: string
* priority: int (1-5)
* dateCreated: DateTime
* dateCompleted: DateTime
* dateDeadline: DateTime
* state: int
  * 0: offen
  * 1: in Bearbeitung
  * 2: erledigt
  * 3: abgebrochen

### report

* openTaskCount: int
* openOverdueTaskCount: int
* inProgressTaskCount: int
* cancelledTaskCount: int
* finishedTaskCount: int
* finishedOverdueTaskCount: int
* createdTaskCount: int
* urgentTasks: Array of [tasks](#task)
