# Simple todolist

A simple todolist build with ReactJS and Framework7 frontend frameworks and a home made PHP API framework.
This example implements CRUD with ReactJS, Redux and Redux-Saga.

## Getting Started

Before starting, ensure to have [docker](https://docs.docker.com/install/) and [npm](https://www.npmjs.com/get-npm) installed. If not, it's time to.

### Prerequisites

Clone the project then enter into.

```
git clone https://github/ilsduc/todolist.git
cd todolist
```

### Demo

See the live demo [application](https://todolist.ilsduc.fr/) and the [api documentation](https://api.todolist.ilsduc.fr/documentation)

### Dependencies

Run the following command in root folder

```
npm run init-project
```

Or run the two parts seperatly
Api server:
* ``` cd api && docker-compose up -d  // run API server ```
Express server:
* ``` cd app && npm run start  // run ExpressJS server ```
### Installing

Then run the project

```
npm run start
```

Or run the two parts seperatly
Api server:
* ``` cd api && docker-compose up -d  // run API server ```
Express server:
* ``` cd app && npm run start  // run ExpressJS server ```
