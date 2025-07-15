# UML: Entity Sync & GUID Management

@startuml
class EPM_Assets {
  +suitecrm_guid: string
  +wp_guid: string
  +contact_id: string
  +lead_id: string
  +asset_type: string
  +asset_value: float
  +date_acquired: date
}

class EPM_Gifts {
  +suitecrm_guid: string
  +wp_guid: string
  +contact_id: string
  +lead_id: string
  +gift_type: string
  +gift_value: float
  +date_given: date
}

class EPM_Liabilities {
  +suitecrm_guid: string
  +wp_guid: string
  +contact_id: string
  +lead_id: string
  +liability_type: string
  +liability_value: float
  +date_incurred: date
}

EPM_Assets --> Contacts
EPM_Gifts --> Contacts
EPM_Liabilities --> Contacts
Contacts <--> Leads
@enduml
