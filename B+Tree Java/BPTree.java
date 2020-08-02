

public class BPTree {
    
    public class BPNode {
        public int[] m_keys;
        public BPNode[] m_children;
        public BPNode m_linkedNode;
        public int m_maxKeys;
        protected int m_keyCount;
        protected int m_childCount;
        public boolean m_isLeaf;
        public boolean m_isRoot;
        public BPNode m_parent;

        
        public BPNode(final int maxKeys) {
            m_maxKeys = maxKeys;
            m_keys = new int[maxKeys + 1];
            m_children = new BPNode[maxKeys + 2];
            m_keyCount = 0;
            m_childCount = 0;   
        }

        // For non-root nodes
        public boolean validNode(){
            return (m_isRoot && m_keyCount > 0) || (m_keyCount >= m_maxKeys/2 && (m_childCount == m_keyCount + 1 || m_isLeaf));
        }

        // For leaf nodes, to check if an insert would require a shuffle
        public boolean insertWithoutProblem() {
            return m_keyCount == m_maxKeys;
        }

        // For leaf nodes, to check if an insert would require a shuffle
        public boolean deleteWithoutProblem() {
            return m_keyCount > m_maxKeys/2;
        }
    
        // Return false if overflow, true otherwise
        public boolean insertKey(final int newKey) {
            // If the node isn't full, we can follow a simple process
            for(int i = 0; i < m_keyCount; i++) {
                if(m_keys[i] == newKey) {
                    return true;
                } else if(m_keys[i] > newKey) {
                    for(int j = m_keyCount; j > i; j--) {
                        m_keys[j] = m_keys[j-1];
                    }
                    m_keys[i] = newKey;
                    m_keyCount++;
                    return true;
                }
            }

            // if code reaches this point, that means the new key needs to be inserted at the tail end
            m_keys[m_keyCount] = newKey;
            m_keyCount++;

            if(m_keyCount == m_maxKeys) 
                return false;
            else
                return true;

        }

        protected BPNode splitNode() {
            BPNode newNode = new BPNode(m_maxKeys);

            newNode.m_parent = m_parent;
            newNode.m_linkedNode = m_linkedNode;
            m_linkedNode = newNode;
            int idx = 0;
            m_keyCount = m_maxKeys/2;

            for(int i = m_maxKeys/2; i < m_maxKeys; i++) {
                newNode.m_keys[idx] = m_keys[i];

                idx++;
                m_keyCount--;
            }

            return newNode;
        }


        // HELPERS
        public BPNode findNode(final int val) {
            if(m_isLeaf) 
                return this;

            for(int i = 0; i < m_keyCount; i++) {
                if(m_keys[i] > val) {
                    return m_children[i].findNode(val);
                }
            }

            return m_children[m_keyCount].findNode(val);
            
        }

        /// Finds the index of the value, or the index of the smallest key greater than the value
        public int findValIndex(final int val) {
            for(int i = 0; i < m_keyCount; i++) {
                if(val <= m_keys[i]) {
                    return i;
                }
            }

            return -1;
        }

    }

    public BPNode m_rootNode;

    public BPTree(final int maxKeys) {
        m_rootNode = new BPNode(maxKeys);
        m_rootNode.m_isRoot = true;
    }    

    public boolean InsertKey(int key) {
        if(m_rootNode.m_childCount < 2) {
            return false;
        }

        BPNode insertTo = findNode(key);
        return false;
    }

    public int[] findKeys(final int minVal, final int maxVal) {
        final BPNode minValNode = findNode(minVal);
        BPNode parseNode = minValNode;
        if(parseNode == null) {
            return new int[0];
        }

        final int startIdx = parseNode.findValIndex(minVal);
        int idx = startIdx;
        int curKey = parseNode.m_keys[idx];

        int count = 0;
        
        while(curKey <= maxVal) {
            count++;
            idx++;

            if(idx >= parseNode.m_maxKeys) {
                parseNode = parseNode.m_linkedNode;
                idx = 0;
            }

            if(parseNode == null) {
                break;
            }
            
            curKey = parseNode.m_keys[idx];
        }

        parseNode = minValNode;
        idx = startIdx;
        final int[] keys = new int[count];

        for(int i = 0; i < count; i++) {
            keys[i] = parseNode.m_keys[idx];

            idx++;

            if(idx >= parseNode.m_maxKeys) {
                parseNode = parseNode.m_linkedNode;
                idx = 0;
            }
        }

        return keys;
    }

    private BPNode findNode(final int val) {
        return m_rootNode.findNode(val);
    }
}