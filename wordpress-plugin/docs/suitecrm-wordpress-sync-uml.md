# UML – SuiteCRM–WordPress Sync (July 2025)

@startuml
class SuiteCRMContact {
  +id: string
  +first_name: string
  +last_name: string
  +email: string
  +phone_work: string
  +phone_mobile: string
  +account_id: string
  +wp_guid: string
}

class EPM_SuggestedUpdate {
  +id: int
  +client_id: int
  +section: string
  +field: string
  +old_value: string (JSON)
  +new_value: string (JSON)
  +notes: string
  +status: string
  +created_at: datetime
  +updated_at: datetime
  +source: string
  +source_record_id: string
}

class EPM_Client {
  +id: int
  +suitecrm_guid: string
  +wp_guid: string
}

SuiteCRMContact "1" -- "*" EPM_SuggestedUpdate : triggers
EPM_Client "1" -- "*" EPM_SuggestedUpdate : has
EPM_Client "1" -- "*" SuiteCRMContact : maps
@enduml
