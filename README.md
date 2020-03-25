# Logging API requests

DreamFactory 4.0.1 and newer supports a very simple solution for logging all requests.
All records requested in the API will be written to the database.
Logging API requests service currently supports only MongoDB.

## Configuration

To configure the logging of all requests, you need to go to the file
with the environment variables and find the LogsDB settings block.

This block contains the following variables:

- `LOGSDB_ENABLED` - boolean flag to enable or disable logging of API requests.
  Possible values: `"true"` or `"false"`. The default is `"false"`.
- `LOGSDB_HOST` - the host of the database to store the logs. The default is `localhost`.
- `LOGSDB_PORT` - the database connection port. The default is `27017`
- `LOGSDB_DATABASE` - the name of the database for logging.
- `LOGSDB_USERNAME` - username with database access permissions.
- `LOGSDB_PASSWORD` - user password.

If `LOGSDB_ENABLED` is set to false, no request will be logged. Also, migration will not be run.

## Log structure

### MongoDB document

Each request will be stored as a separate document in the `access` collection provided database.

Each document will be as follows:

```text
{
  "_id": ObjectId,
  "timestamp": string,
  "method": string,
  "uri" : string,
  "body" : string,
  "expireAt" : ISODate
}
```

- `_id` - Unique ID of document. [MongoDB ObjectId docs](https://docs.mongodb.com/manual/reference/method/ObjectId/).
- `timestamp` - Document creation date. Format "YYYY-mm-dd HH-MM-SS"
- `method` - Request method. For example: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`.
- `uri` - Request path. For example: `/api/v2/system/environment`, `/api/v2/system`.
- `body` - Request body in the string format. For example: `"{\"group\":\"source control,file\"}"`, `"{\"include_count\":\"true\"}"`
- `expireAt` - The date this log will be deleted. By default, 45 days after creation. [MongoDB Date docs](https://docs.mongodb.com/manual/reference/method/Date/)
