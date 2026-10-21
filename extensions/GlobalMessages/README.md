## 🧞 Configurations
**Available configurations**

| ⚙️ | Name | Description | Values | Default
:--- | :--- | :--- | :--- | :---
| ⚙️ | `$wgGlobalMessagesCentralWiki` | Name of the central wiki database where the global messages will be obtained. | `db name` | `null`
| ⚙️ | `$wgGlobalMessagesReadFromDb` | Read and update messages from the specified central wiki database | `true` or `false` | `false`

> [!WARNING]
> Currently it is not known why the database must be created manually, whether it is an error or intentional.

### Permissions
GlobalMessages add the permission `editglobalinterface` you can add it to any of your groups respectively, for example:

```$wgGroupPermissions['staff']['editglobalinterface'] = true```

## 🕹️ Usage
Once you have installed the extension you must specify `wgGlobalMessagesCentralWiki` and `$wgGlobalMessagesReadFromDb`. Once this is done you must manually create the database, you can use clients like dbeaver for this. Otherwise if you don't do this you will see an error that the database cannot be found.

Once this is done, the extension is functional, now you can edit system messages globally for your wikifarm, from the namespace "Global message" and to add different languages, for example if you are adding `Global message:Specialpages-summary` to add a spanish translation it would be `Global message:Specialpages-summary/es` adding: `/` and then the language.